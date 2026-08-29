<?php

namespace App\Application\Project\Form;

use App\Application\Project\Dto\ProjectDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', null, ['label' => 'Titre'])->add('slug', null, ['label' => 'Slug'])->add('excerpt', TextareaType::class, ['label' => 'Résumé', 'required' => false, 'attr' => ['rows' => 3]])->add('content', TextareaType::class, ['label' => 'Contenu', 'attr' => ['rows' => 12, 'data-controller' => 'suneditor']])->add('featuredImage', null, ['label' => 'Image principale', 'required' => false])->add('technologies', TextareaType::class, ['label' => 'Technologies', 'help' => 'Une technologie par ligne.', 'attr' => ['rows' => 6]])->add('websiteUrl', null, ['label' => 'URL du site', 'required' => false])->add('repositoryUrl', null, ['label' => 'URL du dépôt', 'required' => false])->add('isFeatured', CheckboxType::class, ['label' => 'Mettre en avant', 'required' => false])->add('status', ChoiceType::class, ['label' => 'Statut', 'choices' => ['Brouillon' => 'draft', 'Publié' => 'published']])->add('publishedAt', DateTimeType::class, ['label' => 'Date de publication', 'widget' => 'single_text', 'required' => false]);
        $builder->get('technologies')->addModelTransformer(new CallbackTransformer(static fn (array $items): string => implode("\n", $items), static fn (?string $value): array => array_values(array_filter(array_map('trim', preg_split('/\R/', $value ?? '') ?: [])))));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProjectDto::class]);
    }
}
