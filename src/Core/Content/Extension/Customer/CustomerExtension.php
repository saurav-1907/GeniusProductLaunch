<?php declare(strict_types=1);

namespace GeniusProductLaunch\Core\Content\Extension\Customer;

use GeniusProductLaunch\Core\Content\ReleaseProduct\ReleaseProductDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\SetNullOnDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new OneToManyAssociationField(
                'releaseProductCustomer',
                ReleaseProductDefinition::class,
                'customer_id'
            ))->addFlags(new SetNullOnDelete()),
        );

    }

    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }
}
