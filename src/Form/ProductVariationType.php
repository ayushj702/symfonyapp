<?php

namespace App\Form;

use App\Entity\ProductVariation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ProductVariationType extends AbstractType
{
    //use forminterface for submit button.
    //for implementing remove here
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Variation Name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('sku', TextType::class, [
                'label' => 'SKU',
                'required' => false,  // Allow this field to be empty for auto-generation
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Auto-generated if left blank',
                ],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantity',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductVariation::class,
        ]);
    }
}
