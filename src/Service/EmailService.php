<?php declare(strict_types=1);

namespace GeniusProductLaunch\Service;

use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class EmailService
{
    private AbstractMailService $mailService;

    private EntityRepository $mailTemplateRepository;

    private LoggerInterface $logger;

    private SystemConfigService $systemConfigService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityRepositoryInterface
     */
    private $currencyRepository;

    public function __construct(
        AbstractMailService $mailService,
        EntityRepository    $mailTemplate,
        LoggerInterface     $logger,
        SystemConfigService $systemConfigService,
        RouterInterface           $router,
        EntityRepositoryInterface $currencyRepository
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplate;
        $this->logger = $logger;
        $this->systemConfigService = $systemConfigService;
        $this->router = $router;
        $this->currencyRepository = $currencyRepository;
    }

    public function sendEmail($emailDetail, $productDetail, $context)
    {
        $mailTemplateName =  $this->systemConfigService->get('productLaunch.settings.mailTemplate');

        $mailTemplate = $this->getMailTemplate($mailTemplateName, $context);
        $mailTranslations = $mailTemplate->getTranslations();
        $mailTranslation = $mailTranslations->filter(function ($element) use ($context) {
            return $element->getLanguageId() === $context->getLanguageIdChain()['0'];
        })->first();
        $htmlCustomContent = $mailTranslation->getContentHtml();

        $firstname = $emailDetail['firstName'];
        $lastName = $emailDetail['lastName'];
        $salesChannelName = $emailDetail['salesChannelName'];
        $email = $emailDetail['email'];

        $salesChannelId = $emailDetail['salesChannelId'];
        $i=1;
        $replaceContent = '';
        foreach ($productDetail as $productData) {
            if ($i % 2 == 1) {
                $replaceContent .= "
                    <div class='cms-listing-row'  style='display: flex;  width: 100%;'>";
            }
            $productName = $productData->getTranslated()['name'];
            $productNumber = $productData->getProductNumber();
            $productDescription = $productData->getTranslated()['description'];
            $productShortDescription = (strlen($productDescription) > 150)?substr($productDescription, 0, 100) : $productDescription;
            $productPrice = $productData->getPrice()->getElements();
            $mediaData = $productData->getMedia();
            $mediaUrl = '';
            $grossPrice = $netPrice = $grossListPrice = $netListPrice = 0 ;
            $currency = $currencySymbol = null;
            foreach ($productPrice as $price) {
                $grossPrice = $price->getGross();
                $netPrice = $price->getNet();
                if ($price->getListPrice() != null) {
                    $grossListPrice = $price->getlistPrice()->getGross();
                    $netListPrice = $price->getlistPrice()->getNet();
                } else {
                    $grossListPrice = 0;
                    $netListPrice = 0;
                }
                $currency = $this->getCurrencySymbol($price->getCurrencyId(), $context);
                $currencySymbol=$currency->getSymbol();
            }
            $productPriceArray = 0;
            if ($emailDetail['displayPrice'] == true) {
                $productPrices = $grossPrice;
                $productListPrices = $grossListPrice;
            } else {
                $productPrices = $netPrice;
                $productListPrices = $netListPrice;
            }

            if ($productListPrices) {
                $productPricedecimal = number_format($productPrices, 2);
                $productPriceArray = '<span style="color: #f4d13a;">'.$currencySymbol. $productPricedecimal .'*</span>'.'<del>'.$currencySymbol.$productListPrices.'*'.'</del>';
            } else {
                $productPricedecimal = number_format($productPrices, 2);
                $productPriceArray = $currencySymbol.''. $productPricedecimal.'*' ;
            }

            foreach ($mediaData as $media) {
                $mediaUrl = $media->getMedia()->getUrl();
            }
            $replacedProduct = str_replace(str_split('\\/:*?"<>|+-% '), '-', $productName);

            $str = ['--', '% ', '%20', '%', '/'];
            $rplc =['-', '-'];

            $replacedProductName = str_replace($str, $rplc, strtolower($replacedProduct));

            $productURL = $_ENV['APP_URL'] . '/' . $replacedProductName. '/'. $productNumber;
            $replacedmediaUrl = str_replace(' ', '%20', $mediaUrl);
            $replaceContent .= '
        <div class="cms-listing-col" style=" width: 50%; padding:0 8px; box-sizing: border-box; margin-bottom: 10px;">
            <a href='.$productURL.' style="text-decoration: none;">
                <div class="card" style="border:none;background-color: #fff;">
                    <div class="card-body" style="padding: 1rem;     position: relative;">
                            <img src='.$replacedmediaUrl.' alt='.$replacedProductName.' style="width:250px; margin:0 auto; display: block; height: 250px; object-fit: contain;">
                            <div class="product-price-info" style=" background: #558394; width: 70px;height: 70px; line-height: 70px; border-radius: 50%; font-size: 11px; position: absolute;
    top: 18px;color: #fff; text-align: center; font-weight: 600;">
                                <span>'.$productPriceArray.'</span>
                            </div>
                            <h4 style=" font-size: 14px;
    text-transform: uppercase; height: auto; min-height: 30px; text-align: center; color: #6f675a;">'.$replacedProductName.'</h4>
                            <button type="button" style="color: #ffffff; text-align: center; background: #c3beb4; margin: 0 auto; display: block; border-radius: 0; border:none;
                            padding: 8px 22px;">More Details</button>
                    </div>
                </div>
           </a>
       </div>';
            if ($i % 2 == 0) {
                $replaceContent .= "</div>";
            }
            $i++;
        }

        if ($i % 2 != 1) {
            $replaceContent .= "</div>";
        }

        $data = new RequestDataBag();
        //setup content
        $htmlCustomContent = $mailTranslation->getContentHtml();

        $org = ["{firstName}", "{lastName}", "{salesChannelName}"];
        $mod = [$firstname, $lastName, $salesChannelName];
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
            $data->set('recipients', [$email => 'bhavika@icreativetechnologies.com']);
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

    //getting currency symbol
    private function getCurrencySymbol($currencyId, $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $currencyId));
        return $this->currencyRepository->search($criteria, $context)->first();
    }
}
