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

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du Quiz',
                'attr' => [
                    'placeholder' => 'Entrez le nom du quiz',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le nom du quiz ne peut pas être vide.',
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
                'mapped' => false, // Ce champ n'est pas mappé à l'entité Quiz
            ])
            ->add('questions', EntityType::class, [
                'class' => Question::class,
                'choice_label' => 'texte',
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'class' => 'questions-container',
                ],
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('q')
                        ->where('q.niveau = :niveau')
                        ->setParameter('niveau', $options['niveau']);

                    if ($options['nomCours']) {
                        $qb->andWhere('q.nomCours = :nomCours')
                            ->setParameter('nomCours', $options['nomCours']);
                    }

                    return $qb;
                },
            ])
            ->add('selectedQuestions', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'selected-questions',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
            'niveau' => 'facile', // Valeur par défaut pour 'niveau'
            'nomCours' => null, // Valeur par défaut pour 'nomCours'
        ]);

        // Définir les types autorisés pour les options
        $resolver->setAllowedTypes('niveau', ['string']);
        $resolver->setAllowedTypes('nomCours', [NomCours::class, 'null']);
    }
}