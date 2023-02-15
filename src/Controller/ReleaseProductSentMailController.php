<?php declare(strict_types=1);

namespace GeniusProductLaunch\Controller;


use GeniusProductLaunch\Service\EmailService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\Context\AbstractSalesChannelContextFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @RouteScope(scopes={"api"})
 */

class ReleaseProductSentMailController extends AbstractController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $newsletterRecipientRepository;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    /**
     * @var EntityRepositoryInterface
     */
    private $productsRepository;
    private $salesChannelRepository;
    private AbstractMailService $mailService;
    private EmailService $emailService;
    private $mailTemplateRepository;
    private $releaseProductRepository;

    public function __construct
    (
        EntityRepositoryInterface $newsletterRecipientRepository,
        SystemConfigService $systemConfigService,
        EntityRepositoryInterface $productsRepository,
        EntityRepositoryInterface $salesChannelRepository,
        AbstractMailService        $mailService,
        EmailService     $emailService,
        EntityRepositoryInterface $mailTemplateRepository,
        EntityRepositoryInterface $releaseProductRepository
    )
    {
        $this->newsletterRecipientRepository = $newsletterRecipientRepository;
        $this->systemConfigService = $systemConfigService;
        $this->productsRepository = $productsRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->mailService = $mailService;
        $this->emailService = $emailService;
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->releaseProductRepository = $releaseProductRepository;
    }
    /**
     * @Route("/api/search-wizzy/releaseProduct",
     *     name="api.action.search.wizzy.release.product.cron", methods={"GET"})
     */

    public function releaseProduct(Context $context): JsonResponse
    {


        $subscriberCustomers = $this->getSubscribeCustomers($context);
        $products = $this->getAllProduct ($context);
        foreach ($subscriberCustomers as $subscriberCustomer) {
            $customerIds[] = $subscriberCustomer->getId();
            $salesChannelId = $subscriberCustomer->getSalesChannelId();
            $salesChannelNames = $this->getSalesChannelName($salesChannelId, $context);
            foreach ($salesChannelNames as $salesChannelName) {
                $salesChannelName = $salesChannelName->getName();
            }
            $releaseProductDetails = array();
            $releaseProductDetails['salesChannelId'] = $subscriberCustomer->getSalesChannelId();
            $releaseProductDetails['salesChannelName'] = $salesChannelName;
            $releaseProductDetails['firstName'] = $subscriberCustomer->getFirstName();
            $releaseProductDetails['lastName'] = $subscriberCustomer->getLastName();
            $releaseProductDetails['email'] = $subscriberCustomer->getEmail();
            $releaseProductInfoData[] = $releaseProductDetails;

            foreach ($releaseProductInfoData as $email) {
                foreach ($products as $product) {
                    $productId = $product->getId();

                    $releaseProductDetails['productData'] = $product;



                    $uniquecustomerIds = array_unique($customerIds);
                    $checkLog = $this->checkEntryExistOrNot($productId, $context);
                    $id = $checkLog->getTotal() == 0 ? Uuid::randomHex():$checkLog->first()->getId();
//        dd($releaseProductInfoData);
//                    dd($email);
                    $this->emailService->sendEmail($email, $product, Context::createDefaultContext());
                    $this->releaseProductRepository->upsert([
                        [
                            'id' => $id,
                            'productId' => $productId,
                            'value' => $uniquecustomerIds,
                            'lastUsageAt'=> date("Y-m-d"),
                        ]
                    ], $context);
                }
            }
        }
       // dd($releaseProductInfoData);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);

    }
    private function getSubscribeCustomers($context):array
    {
        $criteria = new Criteria();
        return $this->newsletterRecipientRepository->search($criteria, $context)->getElements();
    }

    private function getAllProduct($context): array
    {
        $criteria = new Criteria();
        $criteria->addAssociation('media');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->addFilter(new ContainsFilter('releaseDate', date("Y-m-d")));
        return $this->productsRepository->search($criteria, $context)->getElements();
    }

    private function getSalesChannelName($salesChannelId, Context $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $salesChannelId));
        return $this->salesChannelRepository->search($criteria, $context)->getElements();
    }

    public function checkEntryExistOrNot($productId, $context)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('lastUsageAt', date("Y-m-d")));
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        return $this->releaseProductRepository->search($criteria, $context);
    }
}



