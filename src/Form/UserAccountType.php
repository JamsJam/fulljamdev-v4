<?php

namespace App\Form;

use App\Application\Settings\Account\Dto\UserAccountDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, ['label' => 'Prénom', 'empty_data' => ''])
            ->add('lastName', TextType::class, ['label' => 'Nom', 'empty_data' => ''])
            ->add('email', EmailType::class, ['label' => 'Email de connexion', 'empty_data' => ''])
            ->add('phoneNumber', TelType::class, ['label' => 'Téléphone', 'empty_data' => ''])
            ->add('company', TextType::class, ['label' => 'Entreprise', 'empty_data' => ''])
            ->add('jobTitle', TextType::class, ['label' => 'Poste', 'empty_data' => ''])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => false,
                'empty_data' => null,
                'help' => 'Laissez vide pour conserver le mot de passe actuel.',
                'attr' => ['autocomplete' => 'new-password'],
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'empty_data' => null,
                'attr' => ['autocomplete' => 'current-password'],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Mettre à jour le compte']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserAccountDto::class]);
    }
}
