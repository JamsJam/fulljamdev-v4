<?php

namespace App\Application\Page\Block\Library\Planning\Main;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class PlanningBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'planning.main';
    }

    public function label(): string
    {
        return 'planningBlock';
    }

    public function category(): string
    {
        return 'Reservation';
    }

    public function dtoClass(): string
    {
        return PlanningBlockDTO::class;
    }

    public function formType(): string
    {
        return PlanningBlockType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Planning:Main';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_planning_block_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new PlanningBlockDTO();
    }
}
