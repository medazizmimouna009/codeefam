<?php

namespace App\Form;

use App\Entity\Cours;
use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Offer Type',
                'choices' => [
                    'Silver' => 'silver',
                    'Lite' => 'lite',
                    'Pro' => 'pro',
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
            ->add('cour', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => function(Cours $cours) {
                    return $cours->getTitre(); // Display course title
                },
                'label' => 'Course',
                'attr' => ['class' => 'form-control'],
                'required' => false,
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
