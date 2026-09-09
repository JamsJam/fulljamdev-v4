<?php

namespace App\Application\Project\Form;

use App\Application\Project\Dto\ProjectDto;
use App\Entity\Project\Technology;
use App\Repository\Project\TechnologyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ProjectType extends AbstractType
{
    public function __construct(private readonly TechnologyRepository $technologies, private readonly UrlGeneratorInterface $urls)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', null, ['label' => 'Titre'])->add('excerpt', TextareaType::class, ['label' => 'Résumé', 'required' => false, 'attr' => ['rows' => 5, 'maxlength' => 1000]])->add('content', TextareaType::class, ['label' => 'Contenu', 'attr' => ['rows' => 12, 'data-controller' => 'suneditor', 'data-suneditor-max-characters-value' => 50000]])->add('imageFiles', FileType::class, ['label' => 'Images du projet', 'required' => false, 'multiple' => true, 'help' => 'JPEG, PNG ou WebP — 5 Mo maximum par image.', 'attr' => ['accept' => 'image/jpeg,image/png,image/webp']])->add('technologies', TextType::class, ['label' => 'Technologies', 'required' => false, 'help' => 'Recherchez une technologie ou saisissez-en une nouvelle, puis validez avec Entrée.', 'attr' => ['data-controller' => 'technology-select', 'data-technology-select-url-value' => $this->urls->generate('app_dashboard_project_technology_autocomplete'), 'data-technology-select-placeholder-value' => 'Rechercher ou ajouter une technologie…']])->add('websiteUrl', null, ['label' => 'URL du site', 'required' => false])->add('repositoryUrl', null, ['label' => 'URL du dépôt', 'required' => false])->add('isFeatured', CheckboxType::class, ['label' => 'Mettre en avant', 'required' => false]);
        $builder->get('technologies')->addModelTransformer(new CallbackTransformer(
            static fn (array $items): string => implode(',', array_map(static fn (Technology $technology): string => $technology->getName(), $items)),
            function (?string $value): array {
                $resolved = [];
                foreach (array_filter(array_map('trim', explode(',', $value ?? ''))) as $name) {
                    $key = mb_strtolower($name);
                    if (!isset($resolved[$key])) {
                        $resolved[$key] = $this->technologies->findOneByName($name) ?? (new Technology())->setName($name);
                    }
                }

                return array_values($resolved);
            },
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProjectDto::class]);
    }
}
