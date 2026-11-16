<?php

namespace App\Application\LinkTree\Form;

use App\Application\LinkTree\Dto\LinkTreeDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkTreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, ['label' => 'Nom', 'required' => false])
            ->add('company', TextType::class, ['label' => 'Entreprise', 'required' => false])
            ->add('profilePictureFile', FileType::class, ['label' => 'Photo de profil', 'required' => false, 'help' => 'JPEG, PNG ou WebP — 5 Mo maximum.', 'attr' => ['accept' => 'image/jpeg,image/png,image/webp']])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer les paramètres']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => LinkTreeDto::class]);
    }
}
