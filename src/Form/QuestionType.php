<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\NomCours;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Texte', TextType::class, [
                'label' => 'Contenu Question',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Entrez la question'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le texte de la question ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 255,
                        'minMessage' => 'Le texte de la question doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le texte de la question ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('nomCours', EntityType::class, [
                'class' => NomCours::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisissez un cours',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotNull([
                        'message' => 'Veuillez sélectionner un cours.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}