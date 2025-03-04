<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class ReponseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('texte', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le texte de la réponse ne peut pas être vide.',
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => 'Le texte de la réponse doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le texte de la réponse ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('estCorrecte', null, [
                'label' => 'Est correcte ?',
                'required' => false,
            ])
            ->add('question', EntityType::class, [
                'class' => Question::class,
                'choice_label' => 'texte',
                'placeholder' => 'Choisissez une question',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une question.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
        ]);
    }
}