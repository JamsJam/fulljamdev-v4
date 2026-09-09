<?php

namespace App\Tests\Reservation\Integration\Form;

use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Summary;
use App\Form\AppointmentRescheduleType;
use App\Form\AppointmentSummaryType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class AppointmentDashboardFormTest extends KernelTestCase
{
    public function testRescheduleFormUpdatesAppointmentDates(): void
    {
        self::bootKernel();
        $appointment = (new Appointment())
            ->setStartAt(new \DateTimeImmutable('2026-08-20 10:00:00'))
            ->setEndAt(new \DateTimeImmutable('2026-08-20 10:30:00'));

        $form = self::getContainer()->get(FormFactoryInterface::class)->create(
            AppointmentRescheduleType::class,
            $appointment,
            ['csrf_protection' => false],
        );
        $form->submit([
            'startAt' => '2026-08-21T14:00',
            'endAt' => '2026-08-21T14:30',
        ]);

        self::assertTrue($form->isValid());
        self::assertSame('2026-08-21 14:00', $appointment->getStartAt()?->format('Y-m-d H:i'));
        self::assertSame('2026-08-21 14:30', $appointment->getEndAt()?->format('Y-m-d H:i'));
    }

    public function testSummaryFormRejectsEmptyContentAndAcceptsCompleteSummary(): void
    {
        self::bootKernel();
        $factory = self::getContainer()->get(FormFactoryInterface::class);

        $emptyForm = $factory->create(AppointmentSummaryType::class, new Summary(), ['csrf_protection' => false]);
        $emptyForm->submit(['content' => '', 'internalNotes' => '', 'recordingLink' => '']);
        self::assertFalse($emptyForm->isValid());

        $summary = new Summary();
        $form = $factory->create(AppointmentSummaryType::class, $summary, ['csrf_protection' => false]);
        $form->submit([
            'content' => 'Les prochaines étapes ont été validées.',
            'internalNotes' => 'Relancer dans une semaine.',
            'recordingLink' => 'https://example.com/recording',
        ]);

        self::assertTrue($form->isValid());
        self::assertSame('Les prochaines étapes ont été validées.', $summary->getContent());
        self::assertSame('Relancer dans une semaine.', $summary->getInternalNotes());
    }
}
