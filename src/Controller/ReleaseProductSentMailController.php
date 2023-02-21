<?php declare(strict_types=1);

namespace GeniusProductLaunch\Controller;

use GeniusProductLaunch\Service\EmailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @var EntityRepositoryInterface
     */
    private $productsRepository;

    /**
     * @var EntityRepositoryInterface
     */
    private $salesChannelRepository;

    private EmailService $emailService;

    /**
     * @var EntityRepositoryInterface
     */
    private $releaseProductRepository;

    public function __construct(
        EntityRepositoryInterface $newsletterRecipientRepository,
        EntityRepositoryInterface $productsRepository,
        EntityRepositoryInterface $salesChannelRepository,
        EmailService     $emailService,
        EntityRepositoryInterface $releaseProductRepository
    ) {
        $this->newsletterRecipientRepository = $newsletterRecipientRepository;
        $this->productsRepository = $productsRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->emailService = $emailService;
        $this->releaseProductRepository = $releaseProductRepository;
    }

    /**
     * @Route("/api/search-wizzy/releaseProduct",
     *     name="api.action.search.wizzy.release.product.cron", methods={"GET"})
     * @param Context $context
     * @return JsonResponse
     */

    public function releaseProduct(Context $context): JsonResponse
    {
        $subscriberCustomers = $this->getSubscribeCustomers($context);
        $products = $this->getAllProduct($context);
        foreach ($subscriberCustomers as $subscriberCustomer) {
            $customerIds[] = $subscriberCustomer->getId();
            $salesChannelId = $subscriberCustomer->getSalesChannelId();
            $salesChannelNames = $this->getSalesChannelName($salesChannelId, $context);
            $salesChannelName = '';
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
        }
        foreach ($releaseProductInfoData as $email) {
            foreach ($products as $product) {
                $productId = $product->getId();
            }
            $checkLog = $this->checkEntryExistOrNot($productId, $context);
            $id = $checkLog->getTotal() == 0 ? Uuid::randomHex():$checkLog->first()->getId();
            $uniquecustomerIds = array_unique($customerIds);
            $this->emailService->sendEmail($email, $products, Context::createDefaultContext());
            $this->releaseProductRepository->upsert([
                [
                    'id' => $id,
                    'productId' => $productId,
                    'value' => $uniquecustomerIds,
                    'lastUsageAt'=> date("Y-m-d"),
                ]
            ], $context);
        }
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
