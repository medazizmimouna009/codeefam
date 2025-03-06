<?php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\NomCours;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints as Assert;


class QuizType extends AbstractType
{
   // src/Form/QuizType.php

public function buildForm(FormBuilderInterface $builder, array $options): void
{

$builder
    ->add('nom', TextType::class, [
        'constraints' => [
            new Assert\NotBlank([
                'message' => 'Entrer nom de quiz',
            ]),
            new Assert\Type([
                'type' => 'string',
                'message' => 'Le nom doit être une chaîne de caractères.',
            ]),
        ],
        'attr' => [
            'class' => 'form-control',
            'placeholder' => 'Entrez le nom du quiz'
        ]
    ])

        ->add('niveau', ChoiceType::class, [
            'label' => 'Niveau de difficulté',
            'choices' => [
                'Facile' => 'facile',
                'Moyen' => 'moyen',
                'Difficile' => 'difficile',
            ],
            'attr' => [
                'class' => 'niveau-select',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez sélectionner un niveau de difficulté.',
                ]),
            ],
        ])
        ->add('nomCours', EntityType::class, [
            'class' => NomCours::class,
            'choice_label' => 'nom',
            'placeholder' => 'Sélectionnez un cours',
            'attr' => [
                'class' => 'nom-cours-select',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez sélectionner un cours.',
                ]),
            ],
        ])
        ->add('nombreQuestions', IntegerType::class, [
            'label' => 'Nombre de questions à générer',
            'attr' => [
                'placeholder' => 'Entrez le nombre de questions',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un nombre de questions.',
                ]),
            ],
            'mapped' => false, // This field is not mapped to the Quiz entity
        ]);

    // Remove the 'questions' field from the form
    // We will handle it manually in the controller
}

public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Quiz::class,
        'allow_extra_fields' => true, // Allow extra fields to be submitted
    ]);
}

  
}