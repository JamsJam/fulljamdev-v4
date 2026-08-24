<?php

namespace App\Application\Page\Element\Attribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class HtmlAttributesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CallbackTransformer(
            static function (?array $attributes): array {
                $rows = [];
                foreach ($attributes ?? [] as $name => $value) {
                    $rows[] = new HtmlAttributeDTO((string) $name, (string) $value);
                }

                return $rows;
            },
            static function (?array $rows): array {
                $attributes = [];
                foreach ($rows ?? [] as $row) {
                    if (!$row instanceof HtmlAttributeDTO) {
                        throw new TransformationFailedException('La liste des attributs HTML est invalide.');
                    }
                    if (array_key_exists($row->name, $attributes)) {
                        throw new TransformationFailedException(sprintf('L’attribut « %s » est présent plusieurs fois.', $row->name));
                    }
                    $attributes[$row->name] = $row->value;
                }

                return $attributes;
            },
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => HtmlAttributeType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'required' => false,
            'label' => 'Attributs HTML',
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
