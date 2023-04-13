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
     * @var EntityRepository
     */
    private $currencyRepository;

    /**
     * @var EntityRepository
     */
    private $productRepository;

    public function __construct(
        AbstractMailService $mailService,
        EntityRepository    $mailTemplate,
        LoggerInterface     $logger,
        SystemConfigService $systemConfigService,
        RouterInterface           $router,
        EntityRepository $currencyRepository,
        EntityRepository $productRepository
    ) {
        $this->mailService = $mailService;
        $this->mailTemplateRepository = $mailTemplate;
        $this->logger = $logger;
        $this->systemConfigService = $systemConfigService;
        $this->router = $router;
        $this->currencyRepository = $currencyRepository;
        $this->productRepository = $productRepository;
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
        $manufacturerMediaData = null;
        $replaceContent = '';
        foreach ($productDetail as $productData) {
            $parentId = $productData->getParentId();
            $productNumber = $productData->getProductNumber();
            if ($i % 2 == 1) {
                $replaceContent .= "<table style='width:600px'><tr><td> <div
                    <div class='cms-listing-row' style='display: flex; width: 100%;'>";
            }


            if ($parentId != null) {
                $productName = $this->getVariantProductName($parentId, $context);
                $getCover = $this->getVariantProductName($parentId, $context)->getCover()->getMedia()->getUrl();
                $productDescription = $this->getVariantProductName($parentId, $context)->getDescription();
                $productPrice = $this->getVariantProductName($parentId, $context)->getPrice();
                $manufacturerMediaDatas = $this->getVariantProductName($parentId, $context)->getChildren()->getElements();
                foreach ($manufacturerMediaDatas as $key => $manufacturerMediaUrls) {
                    if ($manufacturerMediaUrls->getProductNumber() == $productNumber) {
                        $manufacturerMediaData = $manufacturerMediaUrls->getManufacturer()->getMedia()->getUrl();
                    }
                }
            } else {
                $productName = $productData->getTranslated()['name'];
                $productDescription = $productData->getTranslated()['description'];
                $getCover = $productData->getCover()->getMedia()->getUrl();
                $productDescription = $productData->getDescription();
                $productPrice = $productData->getPrice();
                $manufacturerMediaDatas = $productData->getManufacturer();
                if ($manufacturerMediaDatas->getMedia()) {
                    $manufacturerMediaData = $manufacturerMediaDatas->getMedia()->getUrl();
                }
            }

            if ($i % 4 == 1) {
                $replaceContent .= "<div class='cms-listing-row' style='display: flex; flex-wrap: wrap;  width: 100%;'>";
            }
            $productShortDescription = (strlen($productDescription) > 150)?substr($productDescription, 0, 100) : $productDescription;
            $mediaUrl = '';
            $grossPrice = $netPrice = $grossListPrice = $netListPrice = 0 ;
            $manufacturerData  = $manufacturerMediaDataUrl = $currencySymbol = null;
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
                $productPriceArray = '<span style="color: #f4d13a;">'.$currencySymbol. $productPrices .'*</span>'.'<del>'.$currencySymbol.$productListPrices.'*'.'</del>';
            } else {
                $productPriceArray = $currencySymbol.''. $productPrices.'*' ;
            }

            if ($manufacturerMediaData != null) {
                $manufacturerData = "<img src='$manufacturerMediaDataUrl' style='width:100px; margin:0 auto; display: block; height: 100px; object-fit: contain;'>";
            }
            if ($getCover == null) {
                $mediaUrl = $this->getProductMediaData($parentId, $context)->getMedia()->getUrl();
            }

            $manufacturerName = str_replace(' ', '-', $productName);
            $replacedmediaUrl = str_replace(' ', '%20', $mediaUrl);
            $productURL = $this->router->generate('frontend.detail.page', ['productId' => $productData->getId() ], UrlGeneratorInterface::ABSOLUTE_URL);
            $replaceContent .= '
        <div class="cms-listing-col" style=" width: 50%; padding:0 8px; box-sizing: border-box; margin-bottom: 10px;">
            <a href='.$productURL.' style="text-decoration: none;">
                <div class="card" style="border:1px solid #bcc1c7;background-color: #fff;">
                    <div class="card-body" style="padding: 1rem;     position: relative;">
                            <img src='.$replacedmediaUrl.' alt='.$productName.' style="width:100px; margin:0 auto; display: block; height: 100px; object-fit: contain;">
                            <div class="product-price-info" style=" background: #558394; width: 70px;height: 70px; line-height: 70px; border-radius: 50%; font-size: 11px; position: absolute;
    top: 18px;color: #fff; text-align: center; font-weight: 600;">
                                <span>'.$productPriceArray.'</span>
                            </div>
                            <div>'.$manufacturerData.'</div>

                            <h4 style="    font-size: 14px;
    text-transform: uppercase; height: auto; min-height: 30px; text-align: center; color: #6f675a;">'.$productName.'</h4>
                            <p style="text-align: center; overflow: hidden; height: 40px; min-height: 40px; color:#6f675a">'.$productShortDescription.'</p>
                            <button type="button" style="color: #ffffff; text-align: center; background: #c3beb4; margin: 0 auto;
    display: block;
    border-radius: 0;
    padding: 8px 22px;">More Details</button>
                    </div>
                </div>
           </a>
       </div>';
            if ($i % 2 == 0) {
                $replaceContent .= "</div></div></td></tr></table>";
            }
            $i++;
        }

        if ($i % 4 != 1) {
            $replaceContent .= "</div></div></td></tr></table>";
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

    //getting currency symbol
    private function getCurrencySymbol($currencyId, $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $currencyId));
        return $this->currencyRepository->search($criteria, $context)->first();
    }

    public function getVariantProductName($productData, $context)
    {
        $criteria = new Criteria();
        $criteria->addAssociation('translation');
        $criteria->addAssociation('media');
        $criteria->addAssociation('cover');
        $criteria->addAssociation('manufacturer');
        $criteria->addAssociation('children.manufacturer');
        $criteria->addAssociation('children');
        $criteria->addAssociation('children.media');
        $criteria->addAssociation('children.manufacturer.media');
        $criteria->addAssociation('children.cover');
        $criteria->addFilter(new EqualsFilter('id', $productData));
        return $this->productRepository->search($criteria, $context)->first();
    }

    public function getProductMediaData($parentId, $context)
    {
        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addFilter(new EqualsFilter('id', $parentId));
        return $this->productRepository->search($criteria, $context)->first();
    }

    private function getVariantProductManufactureData($manufacturerMediaData, $context)
    {
        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addFilter(new EqualsFilter('id', $manufacturerMediaData));
        return $this->productRepository->search($criteria, $context)->first();
    }
}
