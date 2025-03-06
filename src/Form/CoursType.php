<?php


namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Cours;
use App\Entity\NomCours;
use App\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

class CoursType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "Le titre est obligatoire."]),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => "Le titre doit contenir au moins {{ limit }} caractères.",
                        'maxMessage' => "Le titre ne peut pas dépasser {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('description', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "La description est obligatoire."]),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => "La description doit contenir au moins {{ limit }} caractères."
                    ])
                ]
            ])
            ->add('duree', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => "La durée est obligatoire."])
                ]
            ])
            ->add('fichierFile', FileType::class, [
                'label' => 'Fichier (PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '500M',
                        'mimeTypes' => ['application/pdf', 'application/x-pdf'],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide.',
                    ]),
                ],
            ])
          
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nomCategorie',
                'placeholder' => 'Choisir une catégorie',
                'attr' => ['class' => 'js-categorie'],
            ])
            ->add('nomCours', ChoiceType::class, [
                'choices' => [],
                'placeholder' => 'Sélectionnez une catégorie d\'abord',
                'attr' => ['class' => 'js-nomCours'],
            ]);
    

        // Ajouter un écouteur d'événements pour filtrer les noms de cours
        $builder->get('categorie')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $categorie = $form->getData();

                if ($categorie) {
                    // Récupérer les noms de cours associés à la catégorie
                    $nomCours = $this->entityManager
                        ->getRepository(NomCours::class)
                        ->findBy(['categorie' => $categorie]);

                    // Ajouter le champ nomCours avec les options filtrées
                    $form->getParent()->add('nomCours', ChoiceType::class, [
                        'choices' => $nomCours,
                        'choice_label' => 'nom',
                        'choice_value' => 'id',
                        'placeholder' => 'Choisir un nom de cours',
                        'attr' => ['class' => 'js-nomCours'],
                    ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}