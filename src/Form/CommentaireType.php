<?php

namespace App\Form;

use App\Entity\Commentaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType; // Add this for the parent field
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('contenu', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Write your comment here...',
                ],
                'label' => false,
            ]);

        // Add a hidden field for the parent comment (used for replies)
        if ($options['is_reply'] ?? false) {
            $builder->add('parent', HiddenType::class, [
                'mapped' => false, // This field is not mapped to the entity
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commentaire::class,
            'is_reply' => false, // Add this option to distinguish between a comment and a reply
        ]);
    }
}