<?php

namespace App\Tests\Reservation\Integration\Workflow;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Entity\Reservation\Appointment;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Registry;

final class AppointmentWorkflowTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testRequestedAppointmentCanBeConfirmedThenCompletedAfterItOccurred(): void
    {
        $appointment = (new Appointment())
            ->setEndAt(new \DateTimeImmutable('-1 hour'));
        $workflow = $this->workflow($appointment);

        self::assertTrue($workflow->can($appointment, 'confirm'));
        $workflow->apply($appointment, 'confirm');
        self::assertSame(AppointmentStatus::CONFIRMED, $appointment->getStatus());
        self::assertNotNull($appointment->getEditedAt());

        self::assertTrue($workflow->can($appointment, 'reschedule'));
        $workflow->apply($appointment, 'reschedule');
        self::assertSame(AppointmentStatus::CONFIRMED, $appointment->getStatus());

        $workflow->apply($appointment, 'mark_held');
        self::assertSame(AppointmentStatus::OCCURRED, $appointment->getStatus());

        $workflow->apply($appointment, 'complete');
        self::assertSame(AppointmentStatus::COMPLETE, $appointment->getStatus());
    }

    public function testProposedAppointmentCanBeConfirmed(): void
    {
        $appointment = (new Appointment())->setStatus(AppointmentStatus::PROPOSED);
        $workflow = $this->workflow($appointment);

        $workflow->apply($appointment, 'confirm');

        self::assertSame(AppointmentStatus::CONFIRMED, $appointment->getStatus());
    }

    #[DataProvider('rejectableStatusProvider')]
    public function testRequestedOrProposedAppointmentCanBeRejected(AppointmentStatus $status): void
    {
        $appointment = (new Appointment())->setStatus($status);
        $workflow = $this->workflow($appointment);

        self::assertTrue($workflow->can($appointment, 'reject'));
        $workflow->apply($appointment, 'reject');

        self::assertSame(AppointmentStatus::REJECTED, $appointment->getStatus());
    }

    public static function rejectableStatusProvider(): iterable
    {
        yield 'requested' => [AppointmentStatus::REQUESTED];
        yield 'proposed' => [AppointmentStatus::PROPOSED];
    }

    #[DataProvider('pendingStatusProvider')]
    public function testPendingAppointmentCanBeCanceledBeforeConfirmation(AppointmentStatus $status): void
    {
        $appointment = (new Appointment())->setStatus($status);
        $workflow = $this->workflow($appointment);

        self::assertTrue($workflow->can($appointment, 'cancel'));
        $workflow->apply($appointment, 'cancel');

        self::assertSame(AppointmentStatus::CANCELLED, $appointment->getStatus());
    }

    public static function pendingStatusProvider(): iterable
    {
        yield 'requested' => [AppointmentStatus::REQUESTED];
        yield 'proposed' => [AppointmentStatus::PROPOSED];
    }

    public function testConfirmedAppointmentCanBeCanceledOrMarkedAsNoShow(): void
    {
        $cancelled = (new Appointment())->setStatus(AppointmentStatus::CONFIRMED);
        $this->workflow($cancelled)->apply($cancelled, 'cancel');
        self::assertSame(AppointmentStatus::CANCELLED, $cancelled->getStatus());

        $noShow = (new Appointment())
            ->setStatus(AppointmentStatus::CONFIRMED)
            ->setEndAt(new \DateTimeImmutable('-1 hour'));
        $this->workflow($noShow)->apply($noShow, 'no_show');
        self::assertSame(AppointmentStatus::NO_SHOW, $noShow->getStatus());
    }

    public function testFutureConfirmedAppointmentCannotBeMarkedAsHeldOrNoShow(): void
    {
        $appointment = (new Appointment())
            ->setStatus(AppointmentStatus::CONFIRMED)
            ->setEndAt(new \DateTimeImmutable('+1 hour'));
        $workflow = $this->workflow($appointment);

        self::assertFalse($workflow->can($appointment, 'mark_held'));
        self::assertFalse($workflow->can($appointment, 'no_show'));
    }

    private function workflow(Appointment $appointment): \Symfony\Component\Workflow\WorkflowInterface
    {
        return static::getContainer()->get(Registry::class)->get($appointment, 'appointment');
    }
}
