<?php

namespace App\Twig\Components\Front;

use App\Entity\Reservation\Planning;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/front/planning-card/PlanningCard.html.twig')]
final class PlanningCard
{
    public Planning $planning;
}
