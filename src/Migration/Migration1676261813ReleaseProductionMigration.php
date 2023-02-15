<?php declare(strict_types=1);

namespace GeniusProductLaunch\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1676261813ReleaseProductionMigration extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1676261813;
    }

    public function update(Connection $connection): void
    {
        // implement update
        $connection->executeStatement("CREATE TABLE `release_product` (
    `id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NOT NULL,
    `value` JSON NOT NULL,
    `last_usage_at` DATE NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.release_product.value` CHECK (JSON_VALID(`value`)),
    KEY `fk.release_product.product_id` (`product_id`,`product_version_id`),
    CONSTRAINT `fk.release_product.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
