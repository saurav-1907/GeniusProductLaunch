<?php declare(strict_types=1);

namespace GeniusProductLaunch;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Uuid\Uuid;

class GeniusProductLaunch extends Plugin
{
    public const TEMPLATE_TYPE_NAME = 'Release Product Launch';

    public const TEMPLATE_TYPE_NAME_DE = 'Freigabe Produkteinführung';

    public const TEMPLATE_TYPE_NAME_IT = 'Rilascio Lancio del prodotto';

    public const TEMPLATE_TYPE_NAME_FR = 'Lancement du produit';
    public const TEMPLATE_TYPE_TECHNICAL_NAME = 'release_product_launch';

    public const SUBJECT_ENG = "Hey {firstName}, we inform that to release product {salesChannelName}";
    public const SUBJECT_DE = "Hallo {firstName}, wir teilen Ihnen mit, dass wir das Produkt {salesChannelName}";
    public const SUBJECT_IT = "Ciao {firstName}, ti informiamo che per rilasciare il prodotto {salesChannelName}";
    public const SUBJECT_FR = "Bonjour {firstName}, nous vous informons que pour lancer le produit {salesChannelName}";
    public const CONTAIN_PLAIN_EN = "Dear {firstName} {lastName},\nWe are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products.\n\n

    {productsTable}

\n\nWe are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations.";

    public const CONTAIN_PLAIN_DE = "Dear {firstName} {lastName},\nWe are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products.\n\n

    {productsTable}

\n\nWe are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations.";

    public const CONTAIN_PLAIN_IT = "Sehr geehrter {firstName} {lastName},\nWir freuen uns, die Einführung unserer neuen Produktlinie bekannt zu geben! Als {salesChannelName} haben wir jedes Produkt auf den Markt gebracht und sichergestellt, dass es unseren hohen Qualitätsstandards entspricht. Sehen Sie sich unsere neue Produktpalette an.\n\n

    {productsTable}

\n\nWir sind bestrebt, Ihnen das bestmögliche Einkaufserlebnis zu bieten, und wir sind zuversichtlich, dass unsere neue Produktlinie Ihre Erwartungen übertreffen wird.";

    public const CONTAIN_PLAIN_FR = "Cher {firstName} {lastName},\NNous sommes heureux d'annoncer le lancement de notre nouvelle gamme de produits ! En tant que {salesChannelName}, nous avons lancé chaque produit, en veillant à ce qu'ils répondent à nos normes de qualité élevées. Jetez un coup d'œil à notre nouvelle gamme de produits.\N- Nous sommes ravis d'annoncer le lancement de notre nouvelle gamme de produits.

    {productsTable}

\nNNous nous engageons à vous offrir la meilleure expérience d'achat possible, et nous sommes convaincus que notre nouvelle gamme de produits dépassera vos attentes.";


    public const CONTAIN_HTML_EN = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Dear {firstName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> We are excited to announce the launch of our new product line! As {salesChannelName}, we have launched each product, ensuring that they meet our high standards for quality. Checkout our new range of products. </p>
            <p style='line-height:18px; letter-spacing:1px;'> {productsTable} </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> We are committed to providing you with the best possible shopping experience, and we are confident that our new product line will exceed your expectations. </p>
        </td>
    </tr>
</table>";

    public const CONTAIN_HTML_DE = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Beste {firstName} {lastName},</p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Wij zijn verheugd de lancering van onze nieuwe productlijn aan te kondigen! Als {salesChannelName}, hebben we elk product gelanceerd en ervoor gezorgd dat ze voldoen aan onze hoge kwaliteitseisen. Bekijk onze nieuwe productlijn. </p>
            <p style='line-height:18px; letter-spacing:1px;'> {productsTable} </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'>WWij zetten ons in om u de best mogelijke winkelervaring te bieden, en we zijn ervan overtuigd dat onze nieuwe productlijn uw verwachtingen zal overtreffen. </p>
        </td>
    </tr>
</table>";

    public const CONTAIN_HTML_IT = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Caro {firstName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Siamo entusiasti di annunciare il lancio della nostra nuova linea di prodotti! Come {salesChannelName}, abbiamo lanciato ogni prodotto, assicurandoci che soddisfino i nostri elevati standard di qualità. Scoprite la nostra nuova gamma di prodotti. </p>
            <p style='line-height:18px; letter-spacing:1px;'> {productsTable} </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'>Ci impegniamo a fornirvi la migliore esperienza di acquisto possibile e siamo certi che la nostra nuova linea di prodotti supererà le vostre aspettative. </p>
        </td>
    </tr>
</table>";


    public const CONTAIN_HTML_FR = "<table style='width:800px;background-color:#ececec; padding-bottom:0px; border-spacing: 0;margin:0 auto;'>
    <tr>
        <td style='text-align: center;'>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Chère {firstName} , </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Nous sommes heureux d'annoncer le lancement de notre nouvelle gamme de produits ! En tant que {salesChannelName}, nous avons lancé chaque produit, en veillant à ce qu'ils répondent à nos normes de qualité élevées. Découvrez notre nouvelle gamme de produits. </p>
            <p style='line-height:18px; letter-spacing:1px;'> {productsTable} </p>
            <p style='line-height:18px;background-color:#ececec;letter-spacing:1px'> Nous nous engageons à vous offrir la meilleure expérience d'achat possible et nous sommes convaincus que notre nouvelle gamme de produits dépassera vos attentes. </p>
        </td>
    </tr>
</table>";


    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->releaseProductEmailRemplate($installContext);
    }

    //install email template
    public function releaseProductEmailRemplate(InstallContext $installContext)
    {
        /**
         * @var EntityRepository $mailTemplateTypeRepository
         */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');

        /**
         * @var EntityRepository $mailTemplateRepository
         */
        $mailTemplateRepository = $this->container->get('mail_template.repository');

        $mailTemplateTypeId = Uuid::randomHex();
        $mailTemplateType = [
            [
                'id' => $mailTemplateTypeId,
                'name' => [
                    'en-GB' => self::TEMPLATE_TYPE_NAME,
                    'de-DE' => self::TEMPLATE_TYPE_NAME_DE,
                    'it-IT' => self::TEMPLATE_TYPE_NAME_IT,
                    'fr-FR' => self::TEMPLATE_TYPE_NAME_FR
                ],
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
                'senderName' => '{{ salesChannel.name }}',
                'subject' => [
                    'en-GB' => self::SUBJECT_ENG,
                    'de-DE' => self::SUBJECT_DE,
                    'it-IT' => self::SUBJECT_IT,
                    'fr-FR' => self::SUBJECT_FR
                ],
                'contentPlain' =>[
                    'en-GB' => self::CONTAIN_PLAIN_EN,
                    'de-DE' => self::CONTAIN_PLAIN_DE,
                    'it-IT' => self::CONTAIN_PLAIN_IT,
                    'fr-FR' => self::CONTAIN_PLAIN_FR
                ],

                'contentHtml' => [
                    'en-GB' => self::CONTAIN_HTML_EN,
                    'de-DE' => self::CONTAIN_HTML_DE,
                    'it-IT' => self::CONTAIN_HTML_IT,
                    'fr-FR' => self::CONTAIN_HTML_FR
                ]
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
        /** @var EntityRepository $mailTemplateTypeRepository */
        $mailTemplateTypeRepository = $this->container->get('mail_template_type.repository');
        /** @var EntityRepository $mailTemplateRepository */
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
