<?php

namespace App\UI\DatePicker\Service;

use App\UI\DatePicker\Builder\DatePickerMonthBuilder;
use App\UI\DatePicker\Dto\DatePickerMonthDto;
use App\UI\DatePicker\Resolver\DatePickerMonthResolver;

/**
 * Point d’entrée du composant DatePicker.
 *
 * Les consommateurs fournissent un mois et les dates sélectionnables ; le
 * service retourne le modèle UI complet attendu par le composant Twig.
 */
final readonly class DatePickerService
{
    public function __construct(
        private DatePickerMonthResolver $monthResolver,
        private DatePickerMonthBuilder $monthBuilder,
    ) {
    }

    /** @param iterable<string> $availableDates */
    public function create(string|\DateTimeInterface $month, iterable $availableDates): DatePickerMonthDto
    {
        $dates = is_array($availableDates) ? array_values($availableDates) : iterator_to_array($availableDates, false);

        return $this->monthBuilder->build(
            $this->monthResolver->resolve($month),
            $dates,
        );
    }

    public function resolveMonth(string|\DateTimeInterface $month): \DateTimeImmutable
    {
        return $this->monthResolver->resolve($month);
    }
}
