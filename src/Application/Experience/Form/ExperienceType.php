<?php

namespace App\Application\Experience\Form;

use App\Application\Experience\Dto\ExperienceDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ExperienceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', null, ['label' => 'Poste'])->add('company', null, ['label' => 'Entreprise'])->add('type', null, ['label' => 'Type'])->add('contractType', null, ['label' => 'Contexte de la mission', 'required' => false])->add('beginAt', DateType::class, ['label' => 'Date de début', 'widget' => 'single_text'])->add('endAt', DateType::class, ['label' => 'Date de fin', 'widget' => 'single_text', 'required' => false])->add('about', TextareaType::class, [
            'label' => 'Réalisations',
            'help' => 'Paragraphes, listes, gras et italique uniquement.',
            'sanitize_html' => true,
            'sanitizer' => 'app.experience_sanitizer',
            'attr' => [
                'rows' => 12,
                'data-controller' => 'suneditor',
                'data-suneditor-profile-value' => 'basic',
            ],
        ])->add('isVisible', CheckboxType::class, ['label' => 'Afficher dans la timeline', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ExperienceDto::class]);
    }
}
