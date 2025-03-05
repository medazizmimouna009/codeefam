<?php

namespace App\Form;

use App\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('password')

            ->add('isVerified')
            ->add('nom')
            ->add('prenom')
            ->add('dateDeNaissance', null, [
                'widget' => 'single_text'
            ])
            ->add('numTel')
            ->add('adresse')
            ->add('specialite')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tuteur::class,
        ]);
    }
}
