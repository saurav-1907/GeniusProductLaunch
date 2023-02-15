<?php declare(strict_types=1);

namespace GeniusProductLaunch\Core\Content\Extension\Product;

use GeniusProductLaunch\Core\Content\ReleaseProduct\ReleaseProductDefinition;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'releaseProduct',
                ReleaseProductDefinition::class,
                'product_id'
            ))->addFlags(new SetNullOnDelete()),
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductDefinition::class;
    }
}
