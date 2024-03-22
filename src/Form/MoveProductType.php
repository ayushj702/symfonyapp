<?php

namespace App\Form;

use App\Entity\Inventory;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class MoveProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'data' => $options['product'],
                'disabled' => true,
            ])
            ->add('targetInventory', EntityType::class, [
                'class' => Inventory::class,
                'choice_label' => function (Inventory $inventory) {
                    return sprintf('%s - %s', $inventory->getShop()->getName(), $inventory->getName());
                },
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
                'label' => 'Quantity to move',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'product' => null,
        ]);
    }

}
