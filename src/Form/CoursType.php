<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Cours;
use App\Entity\NomCours;
use App\Entity\Tuteur;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents; // N'oubliez pas d'importer FormEvents
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class CoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', null, [
            'constraints' => [
                new Assert\NotBlank(['message' => "Le titre est obligatoire."]),
                new Assert\Length([
                    'min' => 5,
                    'max' => 255,
                    'minMessage' => "Le titre doit contenir au moins {{ limit }} caractères.",
                    'maxMessage' => "Le titre ne peut pas dépasser {{ limit }} caractères."
                ])
            ]
        ])
        ->add('description', null, [
            'constraints' => [
                new Assert\NotBlank(['message'=> "La description est obligatoire."]),
                new Assert\Length([
                    'min' => 10,
                    'minMessage' => "La description doit contenir au moins {{ limit }} caractères."
                ])
            ]
        ])
        ->add('prix', null, [
            'constraints' => [
                new Assert\NotBlank(['message' => "Le prix est obligatoire."]),
                new Assert\Positive(['message' => "Le prix doit être un nombre positif."])
            ]
        ])
        ->add('duree', null, [
            'constraints' => [
                new Assert\NotBlank(['message' => "La durée est obligatoire."])
            ]
        ])
        /*->add('fichier', null, [
            'constraints' => [
                new Assert\NotBlank(['message' => "Un fichier doit être sélectionné."])
            ]
        ])*/


        ->add('fichierFile', FileType::class, [
            'label' => 'Fichier (PDF)',
            'mapped' => false, // Ne pas mapper directement à l'entité
            'required' => false, // Facultatif
            'constraints' => [
                new Assert\File([
                    'maxSize' => '24M',
                    'mimeTypes' => ['application/pdf', 'application/x-pdf'],
                    'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide.',
                ]),
            ],
        ])



        ->add('videos', CollectionType::class, [
            'entry_type' => VideoType::class, // Formulaire pour chaque vidéo
            'entry_options' => ['label' => false],
            'allow_add' => true, // Permettre l'ajout de nouvelles vidéos
            'allow_delete' => true, // Permettre la suppression de vidéos
            'by_reference' => false, // Nécessaire pour appeler les méthodes addVideo et removeVideo
            'label' => 'Vidéos',
        ])



        ->add('updatedAt', DateTimeType::class, [
            'widget' => 'single_text',
            'constraints' => [
                new Assert\NotNull(['message' => "La date de mise à jour est obligatoire."])
            ]
        ])
        ->add('categorie', EntityType::class, [
            'class' => Categorie::class,
            'choice_label' => 'nomCategorie',
            'placeholder' => 'Choisir une catégorie',
            'constraints' => [
                new Assert\NotNull(['message' => "Veuillez sélectionner une catégorie."])
            ]
        ])
        ->add('nomCours', EntityType::class, [
            'class' => NomCours::class,
            'choice_label' => 'nom',
            'placeholder' => 'Choisir un nom de cours',
            'constraints' => [
                new Assert\NotNull(['message' => "Veuillez sélectionner un nom de cours."])
            ]
        ])

        ->add('tuteur', EntityType::class, [
            'class' => Tuteur::class, // Utilisez l'entité Tuteur
            'choice_label' => 'nom', // Affichez le nom du tuteur dans la liste déroulante
            'placeholder' => 'Choisir un tuteur', // Optionnel : ajoutez un placeholder
            'constraints' => [
                new Assert\NotNull(['message' => 'Veuillez sélectionner un tuteur.']),
            ],
        ]);

}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
            'tuteur' => null, // Ajout de la variable tuteur

        ]);
    }
}