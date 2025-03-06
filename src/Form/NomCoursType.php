<?php

namespace App\Form;

use App\Entity\NomCours;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class NomCoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le nom du cours est obligatoire."]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => "Le nom du cours doit contenir au moins 3 caractères.",
                        'maxMessage' => "Le nom du cours ne peut pas dépasser 255 caractères."
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-]+$/',
                        'message' => "Le nom du cours ne peut contenir que des lettres, des chiffres, des espaces et des tirets."
                    ])
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'constraints' => [
                    new Assert\NotNull(['message' => "Veuillez sélectionner une catégorie."])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NomCours::class,
        ]);
    }
}