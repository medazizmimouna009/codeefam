<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Offer Type',
                'choices' => [
                    'Service' => 'service',
                    'Product' => 'product',
                    'Subscription' => 'subscription',
                ],
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('duree', TextType::class, [
                'label' => 'Duration (e.g., 1 month, 3 days)',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter duration'],
                'required' => true,
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Price',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Enter the price'],
                'required' => true,
                'scale' => 2, // Ensures two decimal places for price
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
