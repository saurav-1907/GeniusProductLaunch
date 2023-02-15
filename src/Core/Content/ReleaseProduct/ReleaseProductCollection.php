<?php declare(strict_types=1);

namespace GeniusProductLaunch\Core\Content\ReleaseProduct;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @package core
 * @method void                add(ReleaseProductEntity $entity)
 * @method void                set(string $key, ReleaseProductEntity $entity)
 * @method ReleaseProductEntity[]    getIterator()
 * @method ReleaseProductEntity[]    getElements()
 * @method ReleaseProductEntity|null get(string $key)
 * @method ReleaseProductEntity|null first()
 * @method ReleaseProductEntity|null last()
 */
class ReleaseProductCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ReleaseProductEntity::class;
    }
}
