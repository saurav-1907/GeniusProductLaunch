<?php declare(strict_types=1);

namespace GeniusProductLaunch\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class NewProductSentMailTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'launch_new_product';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // 1 Day
    }
}
