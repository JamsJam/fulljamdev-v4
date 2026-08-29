<?php

namespace App\Application\Page\Block\Library\Resume\Timeline;

use App\Application\Page\Element\Heading\HeadingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ResumeTimelineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', HeadingType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ResumeTimelineDTO::class]);
    }
}
