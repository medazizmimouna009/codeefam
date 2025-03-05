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
use App\Entity\User; 
use Symfony\Component\Security\Core\Security; 



class AchatType extends AbstractType

{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('utilisateur', EntityType::class, [
            'class' => User::class,
            'choice_label' => function(User $user) {
                return $user->getId(); // Display course title
            },
            'label' => 'Id Utilisateur',
            'attr' => ['class' => 'form-control'],
            'required' => true,  
        ])

        ->add('typeAchat', ChoiceType::class, [
            'label' => 'Type d’achat',
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
                    'Actif' => 'actif',
                    'Expiré' => 'expiré',
                    'Annulé' => 'annulé',
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
