<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Search;
use App\Entity\Equipment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Rechercher',
                ],
                'required' => false,
            ])
            ->add('equipments', EntityType::class, [
                // looks for choices from this entity
                'class' => Equipment::class,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
            ->add(
                'minCapacity',
                IntegerType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Capacité minimum',
                    ],
                ]
            )
            ->add(
                'maxCapacity',
                IntegerType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Capacité Maximum',
                    ],
                ]
            )
            ->add(
                'minPrice',
                NumberType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Prix Minimum',
                    ],
                ]
            )
            ->add(
                'maxPrice',
                NumberType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Prix Maximum',
                    ],
                ]
            );
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
