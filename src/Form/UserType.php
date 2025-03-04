<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre email.']),
                    new Email(['message' => 'L\'email "{{ value }}" n\'est pas valide.']),
                ],
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer un mot de passe.']),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('isVerified', CheckboxType::class, [
                'required' => false, // Assuming this field is optional
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom.']),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom.']),
                ],
            ])
            ->add('dateDeNaissance', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre date de naissance.']),
                ],
            ])
            ->add('numTel', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre numéro de téléphone.']),
                    new Regex([
                        'pattern' => '/^[529][0-9]{7}$/',
                        'message' => 'Le numéro de téléphone doit commencer par 5, 2 ou 9 et contenir exactement 8 chiffres.',
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre adresse.']),
                ],
            ])
            ->add('bio', TextareaType::class, [
                'required' => false, // Assuming this field is optional
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}