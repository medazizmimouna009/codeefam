<?php
namespace App\Form;

use App\Entity\Cours;
use App\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le titre est obligatoire."]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => "Le titre doit contenir au moins {{ limit }} caractères.",
                        'maxMessage' => "Le titre ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "La description est obligatoire."]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => "La description doit contenir au moins {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('chemainVideo', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le chemin de la vidéo est obligatoire."])
                ]
            ])
            ->add('dateAjout', null, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotNull(['message' => "La date d'ajout est obligatoire."])
                ]
            ])
            ->add('cours', EntityType::class, [
                'class' => Cours::class,
                'choice_label' => 'id',
                'constraints' => [
                    new Assert\NotNull(['message' => "Veuillez sélectionner un cours."])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}