<?php

namespace App\Application\LinkTree\Form;

use App\Application\LinkTree\Dto\LinkDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('label', TextType::class, ['label' => 'Titre du lien'])
            ->add('href', TextType::class, ['label' => 'Adresse du lien', 'attr' => ['placeholder' => 'https://…']])
            ->add('save', SubmitType::class, ['label' => 'Ajouter le lien']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => LinkDto::class]);
    }
}
