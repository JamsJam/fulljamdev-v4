<?php

namespace App\Twig\Components\Form\DatePicker;

use App\UI\DatePicker\Dto\DatePickerMonthDto;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Form:DatePicker', template: 'components/form/date-picker/DatePicker.html.twig')]
final class DatePicker
{
    public FormView $field;

    public DatePickerMonthDto $calendar;

    public string $previousUrl;
    public string $nextUrl;
    public string $id = 'date-picker';
    public string $label = 'Choisir une date';
}
