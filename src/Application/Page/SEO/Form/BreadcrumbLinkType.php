<?php

namespace App\Application\Page\SEO\Form;

use App\Application\Page\SEO\Dto\BreadcrumbLinkDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BreadcrumbLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, ['label' => 'Libellé', 'empty_data' => ''])
            ->add('url', TextType::class, [
                'label' => 'URL',
                'empty_data' => '',
                'help' => 'URL absolue ou chemin commençant par /.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => BreadcrumbLinkDTO::class]);
    }
}
