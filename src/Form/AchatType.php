<?php

namespace App\Form;

use App\Entity\Achat;
use App\Entity\Cours;
use App\Entity\Offre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\User; // Make sure to import the User entity


class AchatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('idUtilisateur', EntityType::class, [
            'class' => User::class,               // The User entity class
            'choice_label' => 'email',            // Display the user's email in the form
            'label' => 'User Email',              // Label to display in the form
            'attr' => ['class' => 'form-control'], // Form control styling
            'required' => true,                   // Make it required
        ])
            ->add('typeAchat', ChoiceType::class, [
                'label' => 'Type of Purchase',
                'choices' => [
                    'Offre' => 'offre',
                    'Cours' => 'cours',
                ],
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('dateAchat', DateTimeType::class, [
                'label' => 'Purchase Date',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'input' => 'datetime',
            ])
         
            ->add('statut', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Pro' => 'pro',
                    'Lite' => 'lite',
                    'Sivler' => 'silver',
                ],
                'attr' => ['class' => 'form-control'],
                'required' => true,
            ])
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => function(Offre $offre) {
                    return $offre->getType() . ' - ' . $offre->getDuree(); // Display type and duration of the offer
                },
                'label' => 'Offer',
                'attr' => ['class' => 'form-control'],
                'required' => false,
            ])
            ->add('cours', EntityType::class, [
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
            'data_class' => Achat::class,
        ]);
    }
}
