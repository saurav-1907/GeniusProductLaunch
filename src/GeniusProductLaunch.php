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

    public const CONTAIN_PLAIN_EN = "Dear {firstName} {lastName},\nWe are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products.\n\n

    {productsTable}

\n\nWe are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations.";

    public const CONTAIN_PLAIN_DE = "Dear {firstName} {lastName},\nWe are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products.\n\n

    {productsTable}

\n\nWe are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations.";

    public const CONTAIN_HTML_EN = "Dear {firstName} {lastName},<br><br>We are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products.<br><br>

{productsTable}

<div style='display: block; width: 100%'>
We are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations.</div>";

    public const CONTAIN_HTML_DE = "Dear {firstName} {lastName},<br><br>We are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products.

{productsTable}

<div style='display: block; width: 100%'>
We are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations.</div>";


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
