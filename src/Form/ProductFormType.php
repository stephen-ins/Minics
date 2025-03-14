<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', TextType::class, [
                'label' => "Référence",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une référence',
                    ]),
                ]
            ])

            ->add('title', TextType::class, [
                'label' => "Titre",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre',
                    ]),
                ]
            ])

            ->add('description', TextType::class, [
                'label' => "Description",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une description',
                    ]),
                ],
                'attr' => [
                    'rows' => 10,
                ],
            ])

            ->add('color', ChoiceType::class, [
                'label' => "Couleur",
                'choices' => [
                    'Blanc' => 'blanc',
                    'Noir' => 'noir',
                    'Gris Sidéral' => 'gris sidéral',
                    'Bleu' => 'bleu',
                    'Rose' => 'rose',
                ]
            ])

            ->add('size', ChoiceType::class, [
                'label' => "Mémoire",
                'choices' => [
                    '128g' => '128g',
                    '256g' => '256g',
                    '512g' => '512g',
                    '1T' => '1T',
                    '2T' => '2T',
                ]
            ])

            ->add('gender', ChoiceType::class, [
                'label' => "Genre",
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                    'Mixte' => 'mixte',
                ]
            ])

            ->add('picture', FileType::class, [
                'label' => "Photo produit",
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '100M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Formats autorisés : jpg/jpeg/png/webp',
                    ]),
                ]
            ])

            ->add('price', NumberType::class, [
                'label' => "Prix",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un prix',
                    ]),
                ]
            ])

            ->add('stock', IntegerType::class, [
                'label' => "Stock",
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un stock',
                    ]),
                ]
            ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
            ])

            // ->add('category') correspond à la relation ManyToOne clé étrangère ici un champ qui provient d'une autre table SQL donc un champ EntityType, cela va générer dans le formulaire, une liste déroulante avec toutes les catégories et les titres des catégories dans les options du selecteur.




            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
