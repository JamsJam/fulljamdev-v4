<?php

namespace App\Application\Reservation\Availability\Mapper;

final class DayMapper
{
    /** @var array<int, string> */
    private const DAYS = [
        1 => 'lundi',
        2 => 'mardi',
        3 => 'mercredi',
        4 => 'jeudi',
        5 => 'vendredi',
        6 => 'samedi',
        7 => 'dimanche',
    ];

    public function numberToDay(int $number): string
    {
        return self::DAYS[$number] ?? throw new \InvalidArgumentException(sprintf('Le numéro du jour doit être compris entre 1 et 7, %d donné.', $number));
    }

    public function dayToNumber(string $day): int
    {
        $normalizedDay = mb_strtolower(trim($day));
        $number = array_search($normalizedDay, self::DAYS, true);

        if (false === $number) {
            throw new \InvalidArgumentException(sprintf('Le jour "%s" est invalide.', $day));
        }

        return $number;
    }
}
