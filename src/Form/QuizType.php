<?php
// src/Form/QuizType.php

namespace App\Form;

use App\Entity\Quiz;
use App\Entity\Question;
use App\Entity\NomCours;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('nomCours', EntityType::class, [
                'class' => NomCours::class,
                'choice_label' => 'nom',
            ]);
            
    }
}
