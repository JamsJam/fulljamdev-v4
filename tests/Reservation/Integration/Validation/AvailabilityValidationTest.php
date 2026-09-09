<?php

namespace App\Tests\Reservation\Integration\Validation;

use App\Application\Reservation\Availability\Dto\AvailabilityDto;
use App\Entity\Reservation\Availability;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AvailabilityValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testAvailabilityAcceptsAValidDayAndTimeRange(): void
    {
        $availability = (new Availability())
            ->setDow(1)
            ->setStartHour(new \DateTimeImmutable('09:00'))
            ->setEndHour(new \DateTimeImmutable('17:00'));

        self::assertCount(0, $this->validator->validate($availability));
    }

    public function testAvailabilityRejectsAnInvalidDayAndTimeRange(): void
    {
        $availability = (new Availability())
            ->setDow(8)
            ->setStartHour(new \DateTimeImmutable('17:00'))
            ->setEndHour(new \DateTimeImmutable('09:00'));

        self::assertGreaterThanOrEqual(2, $this->validator->validate($availability)->count());
    }

    public function testUnavailableDtoDoesNotRequireHours(): void
    {
        $dto = new AvailabilityDto(dow: 1, isAvailable: false);

        self::assertCount(0, $this->validator->validate($dto));
    }

    public function testAvailableDtoRequiresHours(): void
    {
        $dto = new AvailabilityDto(dow: 1, isAvailable: true);

        self::assertCount(2, $this->validator->validate($dto));
    }

    public function testAvailabilityRequiresAtLeastNinetyMinutes(): void
    {
        $availability = (new Availability())
            ->setDow(1)
            ->setStartHour(new \DateTimeImmutable('09:00'))
            ->setEndHour(new \DateTimeImmutable('10:29'));

        self::assertCount(1, $this->validator->validate($availability));
    }

    public function testAvailabilityAcceptsExactlyNinetyMinutes(): void
    {
        $availability = (new Availability())
            ->setDow(1)
            ->setStartHour(new \DateTimeImmutable('09:00'))
            ->setEndHour(new \DateTimeImmutable('10:30'));

        self::assertCount(0, $this->validator->validate($availability));
    }

    public function testAvailableDtoRequiresAtLeastNinetyMinutes(): void
    {
        $dto = new AvailabilityDto(
            dow: 1,
            startHour: new \DateTimeImmutable('09:00'),
            endHour: new \DateTimeImmutable('10:29'),
            isAvailable: true,
        );

        self::assertCount(1, $this->validator->validate($dto));
    }
}
