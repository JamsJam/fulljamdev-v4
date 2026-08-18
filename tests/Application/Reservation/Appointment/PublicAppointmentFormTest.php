<?php

namespace App\Tests\Application\Reservation\Appointment;

use App\Application\Reservation\Appointment\Dto\PublicAppointmentDto;
use App\Form\PublicAppointmentType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class PublicAppointmentFormTest extends KernelTestCase
{
    public function testAppointmentIsBuiltProgressivelyFromItsFragments(): void
    {
        self::bootKernel();
        $formFactory = self::getContainer()->get(FormFactoryInterface::class);
        $dto = new PublicAppointmentDto();
        $form = $formFactory->create(PublicAppointmentType::class, $dto, [
            'csrf_protection' => false,
            'slots' => ['2026-08-19' => ['10:30', '11:00']],
        ]);

        self::assertTrue($form->get('date')->get('value')->has('2026_08_19'));
        self::assertSame('public_appointment', $form->getConfig()->getOption('csrf_token_id'));
        self::assertFalse($form->get('date')->get('value')->getConfig()->getRequired());
        self::assertFalse($form->get('time')->get('value')->getConfig()->getRequired());
        self::assertFalse($form->get('contact')->get('firstName')->getConfig()->getRequired());
        self::assertSame('Europe/Paris', $form->get('time')->get('timezone')->getData());
        self::assertNull($form->get('time')->get('timezone')->getConfig()->getOption('placeholder'));
        self::assertNull($form->get('time')->get('value')->getConfig()->getOption('placeholder'));
        self::assertSame(
            'change->public-booking#selectDate',
            $form->createView()->children['date']->children['value']->children['2026_08_19']->vars['attr']['data-action'] ?? null,
        );
        $timeActions = array_map(
            static fn ($choice): ?string => $choice->vars['attr']['data-action'] ?? null,
            $form->createView()->children['time']->children['value']->children,
        );
        self::assertContains('change->public-booking#selectTime', $timeActions);
        self::assertCount(2, $form->createView()->children['time']->children['value']->children);
        $timezoneChoices = $form->createView()->children['time']->children['timezone']->vars['choices'];
        self::assertMatchesRegularExpression('/^\+\d{2}:\d{2} - /', $timezoneChoices[0]->label);
        $reference = new \DateTimeImmutable('2026-08-19 12:00:00', new \DateTimeZone('UTC'));
        $offsets = array_map(
            static fn ($choice): int => (new \DateTimeZone($choice->value))->getOffset($reference),
            $timezoneChoices,
        );
        $sortedOffsets = $offsets;
        rsort($sortedOffsets);
        self::assertSame($sortedOffsets, $offsets);

        $form->submit([
            'date' => ['value' => '2026-08-19'],
            'time' => ['value' => '10:30', 'timezone' => 'Europe/Paris'],
            'contact' => [
                'firstName' => 'Ada',
                'lastName' => 'Lovelace',
                'email' => 'ada@example.com',
                'phoneNumber' => '0102030405',
                'reason' => 'Échange sur le projet',
            ],
        ]);

        self::assertTrue($form->isSynchronized());
        self::assertSame('2026-08-19', $dto->date->value);
        self::assertSame('10:30', $dto->time->value);
        self::assertSame('Europe/Paris', $dto->time->timezone);
        self::assertSame('Ada', $dto->contact->firstName);
    }
}
