<?php

namespace App\Form;

use App\Application\Reservation\Appointment\Dto\AppointmentTimeDto;
use App\Application\Reservation\Appointment\Service\SlotTimezoneConverter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AppointmentTimeType extends AbstractType
{
    public function __construct(private readonly SlotTimezoneConverter $timezoneConverter)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $selectedDate = $options['selected_date'] ?? array_key_first($options['slots']);
        $times = [];

        foreach (null === $selectedDate ? [] : ($options['slots'][$selectedDate] ?? []) as $time) {
            $label = $this->timezoneConverter->formatTime(
                $selectedDate,
                $time,
                $options['planning_timezone'],
                $options['display_timezone'],
            );
            $times[$label] = $time;
        }

        $builder
            ->add('timezone', ChoiceType::class, [
                'label' => 'Fuseau horaire',
                'required' => false,
                'placeholder' => false,
                'choices' => $this->buildTimezoneChoices($selectedDate),
                'choice_translation_domain' => false,
                'attr' => ['data-action' => 'change->public-booking#selectTimezone'],
            ])
            ->add('value', ChoiceType::class, [
                'label' => 'Choisir une heure',
                'required' => false,
                'placeholder' => false,
                'choices' => $times,
                'expanded' => true,
                'choice_attr' => static fn (): array => [
                    'data-action' => 'change->public-booking#selectTime',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AppointmentTimeDto::class,
            'selected_date' => null,
            'display_timezone' => 'Europe/Paris',
            'planning_timezone' => 'Europe/Paris',
        ]);
        $resolver->setRequired('slots');
        $resolver->setAllowedTypes('slots', 'array');
        $resolver->setAllowedTypes('selected_date', ['null', 'string']);
        $resolver->setAllowedTypes('display_timezone', 'string');
        $resolver->setAllowedTypes('planning_timezone', 'string');
    }

    /** @return array<string, string> */
    private function buildTimezoneChoices(?string $selectedDate): array
    {
        $reference = new \DateTimeImmutable(
            null === $selectedDate ? 'now' : sprintf('%s 12:00:00', $selectedDate),
            new \DateTimeZone('UTC'),
        );
        $timezones = [];

        foreach (\DateTimeZone::listIdentifiers() as $identifier) {
            $offset = (new \DateTimeZone($identifier))->getOffset($reference);
            $sign = $offset < 0 ? '-' : '+';
            $absoluteOffset = abs($offset);
            $label = sprintf(
                '%s%02d:%02d - %s',
                $sign,
                intdiv($absoluteOffset, 3600),
                intdiv($absoluteOffset % 3600, 60),
                $identifier,
            );
            $timezones[] = ['identifier' => $identifier, 'label' => $label, 'offset' => $offset];
        }

        usort($timezones, static function (array $left, array $right): int {
            return $right['offset'] <=> $left['offset'] ?: strcmp($left['identifier'], $right['identifier']);
        });

        $choices = [];
        foreach ($timezones as $timezone) {
            $choices[$timezone['label']] = $timezone['identifier'];
        }

        return $choices;
    }
}
