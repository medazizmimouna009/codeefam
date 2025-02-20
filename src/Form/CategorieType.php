<?php
namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomCategorie', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le nom de la catégorie est obligatoire."]),
                    new Assert\Length([
                        'min' => 3,
                        'max' => 255,
                        'minMessage' => "Le nom de la catégorie doit contenir au moins {{ limit }} caractères.",
                        'maxMessage' => "Le nom de la catégorie ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "La description est obligatoire."]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => "La description doit contenir au moins {{ limit }} caractères."
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}