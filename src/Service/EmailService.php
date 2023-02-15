<?php declare(strict_types=1);

namespace GeniusProductLaunch\Service;

use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class EmailService
{
    private AbstractMailService $mailService;

    private EntityRepository $mailTemplateRepository;

    private LoggerInterface $logger;

    private SystemConfigService $systemConfigService;

    public function __construct(
        AbstractMailService $mailService,
        EntityRepository    $mailTemplate,
        LoggerInterface     $logger,
        SystemConfigService $systemConfigService
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplate;
        $this->logger = $logger;
        $this->systemConfigService = $systemConfigService;
    }

    public function sendEmail($emailDetail, $productDetail, $context)
    {
        //dd($releaseProductInfoData);
//        dd($releaseProductInfoData);
        $mailTemplateName =  $this->systemConfigService->get('productLaunch.settings.mailTemplate');
        $mailTemplate = $this->getMailTemplate($mailTemplateName, $context);
        $mailTranslations = $mailTemplate->getTranslations();
        $mailTranslation = $mailTranslations->filter(function ($element) use ($context) {
            return $element->getLanguageId() === $context->getLanguageIdChain()['0'];
        })->first();
        $htmlCustomContent = $mailTranslation->getContentHtml();
//        dd($productDetails);
        $replaceContent = "<div style='display: flex; flex-wrap: wrap'>";
       // dd($releaseProductInfoData);
        //foreach ($releaseProductInfoData as $productDetail) {
        $firstname = $emailDetail['firstName'];
        $lastName = $emailDetail['lastName'];
        $salesChannelName = $emailDetail['salesChannelName'];
        $email = $emailDetail['email'];
//dd($productDetail);
        $salesChannelId = $emailDetail['salesChannelId'];
        $productName = $productDetail->getTranslated()['name'];
        $productNumber = $productDetail->getProductNumber();
        $productDescription = $productDetail->getTranslated()['description'];
        $productPrice = $productDetail->getPrice()->getElements();
        $mediaData = $productDetail->getMedia();
        $mediaUrl = '';
        $grossPrice = 0 ;
        foreach ($productPrice as $price) {
            $grossPrice = $price->getGross();
        }
        foreach ($mediaData as $media) {
            $mediaUrl = $media->getMedia()->getUrl();
        }
        $replacedProductName = str_replace(' ', '-', $productName);
        $productURL = $_ENV['APP_URL']  . $replacedProductName. '/'. $productNumber;
//            dd($productURL);
        $replaceContent .= '
        <div class="cms-listing-col" style="flex: 0 0 25%;max-width: 25%; padding:0 8px; box-sizing: border-box;">
            <a href='.$productURL.' style="text-decoration: none;">
                <div class="card" style="border:1px solid #bcc1c7;background-color: #fff">
                    <div class="card-body" style="padding: 1rem;">

                            <img src='.$mediaUrl.' alt='.$productName.' style="width:100px; margin:0 auto; display: block;">

                        <h4 style="text-align: center; color: #4a545b;">'.$productName.'</h4>
                        <p style="text-align: center; overflow: hidden;
        text-overflow: ellipsis; color:#4a545b">'.$productDescription.'</p>
                        <div class="product-price-info" style="color: #4a545b; text-align: center; text-decoration: none; ">'.$grossPrice.'</div>
                    </div>
                </div>
           </a>
       </div>';

        $replaceContent .= "</div>";

        $data = new RequestDataBag();
            //setup content
        $htmlCustomContent = $mailTranslation->getContentHtml();

        $org = ["{firstName}", "{lastName}", "{salesChannelName}"];
        $mod   = [$firstname, $lastName, $salesChannelName];
        $replaceCustomContent = str_replace($org, $mod, $htmlCustomContent);

        $replaceCustomContent = str_replace('{productsTable}', $replaceContent, $replaceCustomContent);
        $htmlCustomContentPlain = $mailTemplate->getTranslation('contentPlain');
        $replaceCustomContentPlain = str_replace('{productsTable}', $replaceContent, $htmlCustomContentPlain);

        // replace the subject dynamic content
        $htmlCustomContentPlain = $mailTemplate->getTranslation('subject');
        $replaceHtmlCustomSubject = str_replace('{firstName}', $firstname, $htmlCustomContentPlain);
        $replaceHtmlCustomSubject = str_replace('{lastName}', $lastName, $replaceHtmlCustomSubject);
        $replaceHtmlCustomSubject = str_replace('{salesChannelName}', $salesChannelName, $replaceHtmlCustomSubject);

        //check condition mail translation is null or not
        if ($mailTranslation === null) {
            $data->set('senderName', $mailTranslation->getSenderName());
            $data->set('contentHtml', $replaceCustomContent);
            $data->set('contentPlain', $replaceCustomContentPlain);
            $data->set('subject', $replaceHtmlCustomSubject);
        } else {
            $data->set('senderName', $mailTranslation->getSenderName());
            $data->set('contentHtml', $replaceCustomContent);
            $data->set('contentPlain', $replaceCustomContentPlain);
            $data->set('subject', $replaceHtmlCustomSubject);
        }
        try {
            $data->set('recipients', [$email => $email]);
            $data->set('salesChannelId', $salesChannelId);
                $this->mailService->send($data->all(), $context);
        } catch (\Exception $e) {
                $this->logger->error(
                    "Could not send mail:\n"
                    . $e->getMessage() . "\n"
                    . 'Error Code:' . $e->getCode() . "\n"
                    . "Template data: \n"
                    . json_encode($data->all()) . "\n"
                );
        }
    }
    //getting mail template
    private function getMailTemplate($mailTemplateName, $context): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.name', $mailTemplateName));
        $criteria->addAssociation('translations');
        return $this->mailTemplateRepository->search($criteria, $context)->first();
    }
}
