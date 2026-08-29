<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Page\Block\Library\Planning\Main\PlanningBlockDTO;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Entity\Reservation\Planning;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Page:Block:Planning:Main',
    template: 'components/page/block/planning/main/PlanningBlock.html.twig',
)]
final class PlanningBlock
{
    public PlanningBlockDTO $data;
    public ?int $blockId = null;

    public function __construct(private readonly FindPlanningService $findPlanning)
    {
    }

    public function getPlanning(): ?Planning
    {
        if (null === $this->data->planningId) {
            return null;
        }

        $planning = $this->findPlanning->find($this->data->planningId);

        return null !== $planning && $planning->isActive() ? $planning : null;
    }

    /** @param array<string, mixed> $attributes */
    public function safeAttributes(array $attributes): string
    {
        $html = '';
        foreach ($attributes as $name => $value) {
            if (!is_scalar($value) && null !== $value) {
                continue;
            }
            $normalizedName = strtolower($name);
            if (!in_array($normalizedName, ['id', 'target', 'rel', 'title'], true)
                && !str_starts_with($normalizedName, 'aria-')
                && !str_starts_with($normalizedName, 'data-')) {
                continue;
            }
            $html .= sprintf(' %s="%s"', htmlspecialchars($name, ENT_QUOTES), htmlspecialchars((string) $value, ENT_QUOTES));
        }

        return $html;
    }
}
