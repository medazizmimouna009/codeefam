<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Reponse;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                ],
            ])
            ->add('estCorrecte')
            ->add('question', EntityType::class, [
                'class' => Question::class,
                'choice_label' => 'texte', // Changez 'id' par 'texte' pour afficher le texte de la question
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reponse::class,
            'attr' => ['novalidate' => 'novalidate'], // Disable client-side validation
        ]);
    }
}