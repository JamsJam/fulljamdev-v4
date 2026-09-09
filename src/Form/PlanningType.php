<?php

namespace App\Form;

use App\Application\Reservation\Planner\Dto\PlanningDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Nom du planning',
                'attr' => ['placeholder' => 'Ex. Appels découverte'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'data-controller' => 'suneditor',
                ],
            ])
            ->add('duration', RangeType::class, [
                'label' => 'Durée d’un rendez-vous',
                'attr' => ['min' => 5, 'max' => 60, 'step' => 5],
                'help' => 'Entre 5 et 60 minutes.',
            ])
            ->add('gap', RangeType::class, [
                'label' => 'Délai entre deux rendez-vous',
                'attr' => ['min' => 10, 'max' => 40, 'step' => 5],
                'help' => 'Entre 10 et 40 minutes.',
            ])
            ->add('color', ColorType::class, [
                'label' => 'Couleur',
                'attr' => [
                    'data-planning-color-availability-target' => 'input',
                    'data-action' => 'change->planning-color-availability#check',
                    'aria-describedby' => 'planning-color-availability-message',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlanningDto::class,
        ]);
    }
}
