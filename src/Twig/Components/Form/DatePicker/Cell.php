<?php

namespace App\Twig\Components\Form\DatePicker;

use App\UI\DatePicker\Dto\DatePickerDayDto;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Form:DatePicker:Cell', template: 'components/form/date-picker/Cell.html.twig')]
final class Cell
{
    public DatePickerDayDto $day;

    public ?FormView $choice = null;
}
