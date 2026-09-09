<?php

namespace App\Application\Page\SEO\Form;

use App\Application\Page\SEO\Dto\SeoDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Titre SEO', 'empty_data' => ''])
            ->add('description', TextareaType::class, ['empty_data' => '', 'attr' => ['rows' => 3]])
            ->add('canonicalUrl', UrlType::class, ['label' => 'URL canonique', 'required' => false])
            ->add('noIndex', CheckboxType::class, ['label' => 'Ne pas indexer cette page', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SeoDTO::class]);
    }
}
