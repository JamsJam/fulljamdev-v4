<?php

namespace App\Application\Page\Element\Cta;

use App\Application\Page\Element\Attribute\HtmlAttributesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

final class CtaType extends AbstractType
{
    public function __construct(private readonly RouterInterface $router)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', null, ['empty_data' => ''])
            ->add('target', EnumType::class, [
                'class' => CtaTarget::class,
                'label' => 'Type de destination',
                'choice_label' => static fn (CtaTarget $target): string => match ($target) {
                    CtaTarget::ROUTE => 'Route de l’application',
                    CtaTarget::URL => 'URL personnalisée',
                },
            ])
            ->add('routeName', ChoiceType::class, [
                'label' => 'Route',
                'placeholder' => 'Sélectionnez une route',
                'required' => false,
                'choices' => $this->routeChoices(),
                'choice_translation_domain' => false,
            ])
            ->add('routeParameters', HtmlAttributesType::class, [
                'label' => 'Paramètres de route',
                'required' => false,
            ])
            ->add('href', null, ['label' => 'URL', 'empty_data' => '', 'required' => false])
            ->add('attributes', HtmlAttributesType::class);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event): void {
            $data = $event->getData();
            if (is_array($data) && !isset($data['target'])) {
                $data['target'] = CtaTarget::URL->value;
                $event->setData($data);
            }
        });
    }

    /** @return array<string, array<string, string>> */
    private function routeChoices(): array
    {
        $choices = ['Routes publiques' => [], 'Routes d’administration' => []];

        foreach ($this->router->getRouteCollection() as $name => $route) {
            $methods = $route->getMethods();
            if (str_starts_with($name, '_') || ([] !== $methods && !in_array('GET', $methods, true))) {
                continue;
            }

            $path = $route->getPath();
            $group = str_starts_with($path, '/dashboard') ? 'Routes d’administration' : 'Routes publiques';
            $choices[$group][sprintf('%s — %s', $name, $path)] = $name;
        }

        return array_filter($choices);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => CtaDTO::class]);
    }
}
