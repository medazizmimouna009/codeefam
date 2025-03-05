<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 2,
                    'required' => false,
                    'placeholder' => 'Write your post here...',
                    'style' => 'margin-bottom: 15px; border-radius: 10px; padding: 10px;'
                ],
                'label' => false,
            ])
            ->add('image', FileType::class, [
                'label' => 'Post Image',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'margin-bottom: 15px; border-radius: 10px; padding: 10px;'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
