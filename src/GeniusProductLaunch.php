<?php declare(strict_types=1);

namespace GeniusProductLaunch;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;

class GeniusProductLaunch extends Plugin
{
    public const TEMPLATE_TYPE_NAME = 'Release Product Launch';
    public const TEMPLATE_TYPE_TECHNICAL_NAME = 'release_product_launch';

    public const SUBJECT_ENG = "Hey {firstName}, we inform that to release product {salesChannelName}";

    public const SUBJECT_DE = "Hallo {firstName}, wir teilen das mit, um das Produkt freizugeben {salesChannelName}";

    public const CONTAIN_PLAIN_EN = "Dear {firstName} {lastName},\nWe wanted to thank you for choosing {salesChannelName} for your recent purchase order number
    \nWe're constantly striving to improve and provide the best possible experience for our customers, and your feedback is essential in helping
    us achieve that goal. We'd be grateful if you could take a few minutes to share your thoughts on the products. Your review will not only help us, but it will also assist other customers in their purchasing decisions.
    \nWe'll appreciate your honest feedback on our products. You can visit below product links to write a review:
    Product Name
     {productName} {productDescription}
    \n\nLooking forward to your review and to be your go-to site for Shopping\nThank you again for choosing {salesChannelName}.";

    public const CONTAIN_PLAIN_DE = "Sehr geehrte {firstName} {lastName},\nWir möchten uns bei Ihnen dafür bedanken, dass Sie {salesChannelName} für Ihren letzten Einkauf ausgewählt haben Bestellnummer:
    \nWir sind ständig bestrebt, uns zu verbessern und unseren Kunden das bestmögliche Erlebnis zu bieten, und Ihr Feedback ist entscheidend, um uns dabei zu helfen, dieses Ziel zu erreichen. Wir
    wären Ihnen dankbar, wenn Sie sich ein paar Minuten Zeit nehmen könnten, um uns Ihre Meinung zu den Produkten mitzuteilen. Ihre Bewertung hilft nicht nur uns, sondern auch anderen Kunden bei ihrer Kaufentscheidung.
    \nWir freuen uns über Ihr ehrliches Feedback zu unseren Produkten. Sie können die folgenden Produktlinks besuchen, um eine Bewertung zu schreiben:
     Product Name productURL
     {productName} {productDescription} {productURL}
    \n\nIch freue mich auf Ihre Bewertung und darauf, Ihre Anlaufstelle für Shopping zu sein\nNochmals vielen Dank, dass Sie sich entschieden haben {salesChannelName}.";

    public const CONTAIN_HTML_EN = " Dear {firstName} {lastName},<br><br>We wanted to thank you for choosing {salesChannelName} for your recent purchase order number: <br><br>We're constantly striving to improve and provide the best possible experience for our customers, and your feedback is essential in helping us achieve that goal. We'd be grateful if you could take a few minutes to share your thoughts on the products. Your review will not only help us, but it will also assist other customers in their purchasing decisions.<br><br>We'll appreciate your honest feedback on our products. You can visit below product links to write a review:<br><br>
    <div class='cms-listing-row' style='display: flex;flex-wrap: wrap;'>
    {productsTable}
    </div>
<br>Looking forward to your review and to be your go-to site for Shopping<br>Thank you again for choosing {salesChannelName}.";
    public const CONTAIN_HTML_DE = "Sehr geehrte {firstName} {lastName},<br><br>Wir möchten uns bei Ihnen dafür bedanken, dass Sie {salesChannelName} für Ihren letzten Einkauf ausgewählt haben Bestellnummer: .<br><br>Wir sind ständig bestrebt, uns zu verbessern und unseren Kunden das bestmögliche Erlebnis zu bieten, und Ihr Feedback ist entscheidend, um uns dabei zu helfen, dieses Ziel zu erreichen. Wir wären Ihnen dankbar, wenn Sie sich ein paar Minuten Zeit nehmen könnten, um uns Ihre Meinung zu den Produkten mitzuteilen. Ihre Bewertung hilft nicht nur uns, sondern auch anderen Kunden bei ihrer Kaufentscheidung.<br><br>Wir freuen uns über Ihr ehrliches Feedback zu unseren Produkten. Sie können die folgenden Produktlinks besuchen, um eine Bewertung zu schreiben:<br>
        <div class='cms-listing-row' style='display: flex;flex-wrap: wrap;'>
    {productsTable}

    </div>
    <br>Ich freue mich auf Ihre Bewertung und darauf, Ihre Anlaufstelle für Shopping zu sein<br>Nochmals vielen Dank, dass Sie sich entschieden haben {salesChannelName}.";

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->releaseProductEmailRemplate($installContext);
    }

    //install email template
    public function releaseProductEmailRemplate(InstallContext $installContext)
    {
        /**
         * @var EntityRepositoryInterface $mailTemplateTypeRepository
         */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        /**
         * @var EntityRepositoryInterface $mailTemplateRepository
         */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $mailTemplateTypeId = Uuid::randomHex();
        $mailTemplateType = [
            [
                'id' => $mailTemplateTypeId,
                'name' => self::TEMPLATE_TYPE_NAME,
                'technicalName' => self::TEMPLATE_TYPE_TECHNICAL_NAME,
                'availableProduct' => [
                    'product' => 'product',
                    'salesChannel' => 'sales_channel'
                ]
            ]
        ];

        $mailTemplate = [
            [
                'id' => Uuid::randomHex(),
                'mailTemplateTypeId' => $mailTemplateTypeId,
                'senderName' => [
                    'en-GB' => 'Admin',
                    'de-DE' => 'Administratorin'
                ],
                'subject' => [
                    'en-GB' => self::SUBJECT_ENG,
                    'de-DE' => self::SUBJECT_DE
                ],
                'contentPlain' => [
                    'en-GB' => self::CONTAIN_PLAIN_EN,
                    'de-DE' => self::CONTAIN_PLAIN_DE
                ],
                'contentHtml' => [
                    'en-GB' => self::CONTAIN_HTML_EN,
                    'de-DE' => self::CONTAIN_HTML_DE
                ],
            ]
        ];
        try {
            $mailTemplateTypeRepository->create($mailTemplateType, $installContext->getContext());
            $mailTemplateRepository->create($mailTemplate, $installContext->getContext());
        } catch (UniqueConstraintViolationException $exception) {
        }
    }

    //uninstall mail template and table from database
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `release_product`');
        $connection->executeStatement(
            'DELETE FROM system_config WHERE configuration_key LIKE :domain',
            [
                'domain' => '%productLaunch.settings%',
            ]
        );
        /** @var EntityRepositoryInterface $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepositoryInterface $mailTemplateRepository */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        /** @var MailTemplateTypeEntity $myCustomMailTemplateType */
        $myCustomMailTemplateType = $mailTemplateTypeRepository->search((new Criteria())->addFilter(new EqualsFilter('technicalName', self::TEMPLATE_TYPE_TECHNICAL_NAME)), $uninstallContext->getContext())->first();

        $mailTemplateIds = $mailTemplateRepository->searchIds((new Criteria())->addFilter(new EqualsFilter('mailTemplateTypeId', $myCustomMailTemplateType->getId())), $uninstallContext->getContext())->getIds();

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $mailTemplateIds);

        $mailTemplateRepository->delete($ids, $uninstallContext->getContext());
        $mailTemplateTypeRepository->delete([['id' => $myCustomMailTemplateType->getId()]], $uninstallContext->getContext());
    }
}
