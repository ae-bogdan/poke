<?php

namespace App\Form\Type;

use App\Entity\Pokemon;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PokemonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('team_id', TextType::class, [
                'mapped' => false,
                'required' => false,
                'data' => $options['team_id'],
                'attr' => [
                    'class' => 'd-none',
                ]
            ])
            ->add('team_name', TextType::class, [
                'mapped' => false,
                'required' => true,
                'data' => $options['team_name'],
                'attr' => [
                    'placeholder' => 'Team name...'
                ]
            ])
            ->add('pokemon_id', NumberType::class, [
                'required' => $options['pokemon_required'],
                'attr' => [
                    'class' => 'd-none',
                ]
            ])
            ->add('name', TextType::class, [
                'required' => $options['pokemon_required'],
                'attr' => [
                    'class' => 'd-none',
                ]
            ])
            ->add('exp', NumberType::class, [
                'required' => $options['pokemon_required'],
                'attr' => [
                    'class' => 'd-none',
                ]
            ])
            ->add('type', EntityType::class, [
                'class'     => Type::class,
                'expanded'  => false,
                'multiple'  => true,
                'required' => $options['pokemon_required'],
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'd-none',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pokemon::class,
            'team_id' => null,
            'team_name' => null,
            'pokemon_required' => true,
        ]);
    }
}
