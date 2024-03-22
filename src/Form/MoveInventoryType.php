<?php

namespace App\Form;

use App\Entity\Inventory;
use App\Entity\Shop;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class MoveInventoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $shop = $options['shop'];

        $builder
            ->add('inventoryItem', EntityType::class, [
                'class' => Inventory::class,
                'query_builder' => function (EntityRepository $er) use ($shop) {
                    return $er->createQueryBuilder('i')
                        ->where('i.shop = :shop')
                        ->setParameter('shop', $shop);
                },
                'choice_label' => 'name',
                'label' => 'Inventory Item',
            ])
            ->add('targetShop', EntityType::class, [
                'class' => Shop::class,
                'choice_label' => 'name',
                'label' => 'Target Shop',
                // Exclude the current shop from the target shop options
                'query_builder' => function (EntityRepository $er) use ($shop) {
                    return $er->createQueryBuilder('s')
                        ->where('s.id != :shopId')
                        ->setParameter('shopId', $shop->getId());
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'shop' => null, // Add shop as an option to be passed into the form
        ]);
    }
}
