<?php

namespace App\Application\Reservation\Planner\Service;

use App\Entity\Reservation\Planning;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class PlanningInvitationService
{
    public function __construct(
        private ClockInterface $clock,
        #[Autowire('%kernel.secret%')]
        private string $secret,
    ) {
    }

    public function create(Planning $planning, int $hours): string
    {
        if ($hours < 1 || $hours > 72) {
            throw new \InvalidArgumentException('La durée doit être comprise entre 1 et 72 heures.');
        }
        if (null === $planning->getId() || !$planning->isActive()) {
            throw new \DomainException('Activez le planning avant de créer un lien signé.');
        }

        $expires = (string) ($this->clock->now()->getTimestamp() + $hours * 3600);

        return $expires.'.'.$this->signature($planning, $expires);
    }

    public function canAccess(Planning $planning, string $token = ''): bool
    {
        if (!$planning->isActive()) {
            return false;
        }
        if ($planning->isOnline()) {
            return true;
        }
        if (null === $planning->getId() || 1 !== preg_match('/^([0-9]{1,12})\.([a-f0-9]{64})$/D', $token, $parts)) {
            return false;
        }

        return (int) $parts[1] > $this->clock->now()->getTimestamp()
            && hash_equals($this->signature($planning, $parts[1]), $parts[2]);
    }

    private function signature(Planning $planning, string $expires): string
    {
        return hash_hmac('sha256', 'planning-invitation|'.$planning->getId().'|'.$planning->getSlug().'|'.$expires, $this->secret);
    }
}
