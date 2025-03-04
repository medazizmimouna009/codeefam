<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\NomCours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('texte', TextType::class, [
                'label' => 'Texte de la question',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le texte de la question ne peut pas être vide.',
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
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un cours.',
                    ]),
                ],
            ])
            ->add('niveau', ChoiceType::class, [
                'label' => 'Niveau de difficulté',
                'choices' => [
                    'Facile' => 'facile',
                    'Moyen' => 'moyen',
                    'Difficile' => 'difficile',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner un niveau de difficulté.',
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