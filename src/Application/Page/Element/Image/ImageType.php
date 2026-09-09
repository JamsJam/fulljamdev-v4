<?php

namespace App\Application\Page\Element\Image;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', EnumType::class, [
                'class' => ImageSource::class,
                'expanded' => true,
                'choice_label' => static fn (ImageSource $source): string => match ($source) {
                    ImageSource::MEDIA => 'Fichier envoyé',
                    ImageSource::URL => 'URL externe',
                },
            ])
            ->add('mediaId', HiddenType::class, ['required' => false])
            ->add('file', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'help' => 'JPEG, PNG, WebP ou GIF — 5 Mo maximum.',
                'attr' => ['accept' => 'image/jpeg,image/png,image/webp,image/gif'],
            ])
            ->add('url', UrlType::class, ['label' => 'URL externe', 'required' => false])
            ->add('alt', null, ['label' => 'Texte alternatif', 'empty_data' => ''])
            ->add('title', null, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ImageDTO::class]);
    }
}
