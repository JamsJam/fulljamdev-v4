<?php

namespace App\Application\Page\Page\Form;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;
use App\Application\Page\Block\Registry\BlockRegistry;
use App\Application\Page\Page\Dto\PageBlockDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PageBlockType extends AbstractType
{
    public function __construct(private readonly BlockRegistry $registry)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('type', HiddenType::class)
            ->add('position', HiddenType::class);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $block = $event->getData();
            if ($block instanceof PageBlockDTO && '' !== $block->type) {
                $this->addDataField($event->getForm(), $this->registry->get($block->type), $block->data);
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $submitted = $event->getData();
            if (!is_array($submitted) || !is_string($submitted['type'] ?? null)) {
                throw new \InvalidArgumentException('Le type du bloc est obligatoire.');
            }

            $current = $event->getForm()->getData();
            $submitted['position'] ??= $current instanceof PageBlockDTO ? $current->position : 0;
            $event->setData($submitted);

            $definition = $this->registry->get($submitted['type']);
            $data = $current instanceof PageBlockDTO && $current->type === $definition->type()
                ? $current->data
                : $definition->createDefaultData();
            $this->addDataField($event->getForm(), $definition, $data);
        });
    }

    private function addDataField(FormInterface $form, BlockDefinitionInterface $definition, object $data): void
    {
        $form->add('data', $definition->formType(), ['label' => false, 'data' => $data]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PageBlockDTO::class, 'empty_data' => static fn (): PageBlockDTO => new PageBlockDTO()]);
    }
}
