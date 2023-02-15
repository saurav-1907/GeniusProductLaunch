<?php declare(strict_types=1);

namespace GeniusProductLaunch\Service\ScheduledTask;

use GeniusProductLaunch\Controller\ReleaseProductSentMailController;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class NewProductSentMailTaskHandler extends ScheduledTaskHandler
{

    /**
     * @var EntityRepositoryInterface
     */
    protected $scheduledTaskRepository;

    /**
     * @var ReleaseProductSentMailController
     */
      private $releaseProductSentMailController;
          /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        releaseProductSentMailController $releaseProductSentMailController,
        SystemConfigService $systemConfigService
    )
    {
        $this->scheduledTaskRepository = $scheduledTaskRepository;
        $this->releaseProductSentMailController = $releaseProductSentMailController;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getHandledMessages(): iterable
    {
        return [NewProductSentMailTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        $active = $this->systemConfigService->get('productLaunch.settings.active');
        if ($active) {
            echo "Hello" ;
            $this->releaseProductSentMailController->releaseProduct($context);
        }
        echo "Hiii" ;
    }
}
