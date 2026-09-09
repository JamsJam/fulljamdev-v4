<?php

namespace App\Application\Page\Block\Library\CardDisplay\Shared;

use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Element\Cta\CtaType;
use App\Application\Page\Element\Image\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CardWithLogoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('logo', ImageType::class)->add('title')->add('text')->add('cta', CtaType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CardDisplayItemDTO::class]);
    }
}
