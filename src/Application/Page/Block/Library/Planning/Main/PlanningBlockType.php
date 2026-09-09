<?php

namespace App\Application\Page\Block\Library\Planning\Main;

use App\Application\Page\Element\Heading\HeadingType;
use App\Application\Page\Element\Text\TextType;
use App\Application\Reservation\Planner\Service\GetPlanningsService;
use App\Entity\Reservation\Planning;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlanningBlockType extends AbstractType
{
    public function __construct(private readonly GetPlanningsService $getPlannings)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', HeadingType::class)
            ->add('text', TextType::class)
            ->add('planningId', ChoiceType::class, [
                'label' => 'Planning à afficher',
                'placeholder' => 'Sélectionnez un planning',
                'choices' => $this->planningChoices(),
                'choice_translation_domain' => false,
            ]);
    }

    /** @return array<string, int> */
    private function planningChoices(): array
    {
        $choices = [];
        foreach ($this->getPlannings->get() as $planning) {
            if (null === $planning->getId()) {
                continue;
            }
            $choices[$this->planningLabel($planning)] = $planning->getId();
        }

        return $choices;
    }

    private function planningLabel(Planning $planning): string
    {
        return sprintf(
            '%s — %d min — %s',
            $planning->getTitle() ?? 'Planning sans nom',
            $planning->getDuration() ?? 0,
            $planning->isActive() ? 'Actif' : 'Inactif',
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => PlanningBlockDTO::class]);
    }
}
