<?php

namespace App\Repository\Reservation;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Entity\Reservation\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointment>
 */
class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    /**
     * @return Appointment[]
     */
    public function findStartingBetween(\DateTimeImmutable $startAt, \DateTimeImmutable $endAt): array
    {
        return $this->createQueryBuilder('appointment')
            ->addSelect('planning')
            ->innerJoin('appointment.planning', 'planning')
            ->andWhere('appointment.startAt >= :startAt')
            ->andWhere('appointment.startAt < :endAt')
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt)
            ->orderBy('appointment.startAt', 'ASC')
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
            ->getResult();
    }

    /**
     * @param list<AppointmentStatus> $statuses
     *
     * @return Appointment[]
     */
    public function findStartingBetweenByStatuses(
        \DateTimeImmutable $startAt,
        \DateTimeImmutable $endAt,
        array $statuses,
    ): array {
        return $this->createQueryBuilder('appointment')
            ->addSelect('planning')
            ->innerJoin('appointment.planning', 'planning')
            ->andWhere('appointment.startAt >= :startAt')
            ->andWhere('appointment.startAt < :endAt')
            ->andWhere('appointment.status IN (:statuses)')
            ->setParameter('startAt', $startAt)
            ->setParameter('endAt', $endAt)
            ->setParameter('statuses', array_map(static fn (AppointmentStatus $status): string => $status->value, $statuses))
            ->orderBy('appointment.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param list<AppointmentStatus> $statuses
     *
     * @return Appointment[]
     */
    public function findByStatuses(array $statuses): array
    {
        return $this->createQueryBuilder('appointment')
            ->addSelect('planning')
            ->innerJoin('appointment.planning', 'planning')
            ->andWhere('appointment.status IN (:statuses)')
            ->setParameter('statuses', array_map(static fn (AppointmentStatus $status): string => $status->value, $statuses))
            ->orderBy('appointment.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return Appointment[] */
    public function findToProcess(\DateTimeImmutable $date): array
    {
        return $this->createQueryBuilder('appointment')
            ->addSelect('planning', 'contact')
            ->addSelect(
                '(CASE
                    WHEN appointment.status = :requested THEN 1
                    WHEN appointment.status = :confirmed THEN 2
                    WHEN appointment.status = :occurred THEN 3
                    ELSE 4
                END) AS HIDDEN statusOrder',
            )
            ->innerJoin('appointment.planning', 'planning')
            ->innerJoin('appointment.contact', 'contact')
            ->leftJoin('appointment.summary', 'summary')
            ->andWhere(
                'appointment.status = :requested
                OR (appointment.status = :confirmed AND appointment.endAt <= :date)
                OR (appointment.status = :occurred AND summary.id IS NULL)',
            )
            ->setParameter('requested', AppointmentStatus::REQUESTED->value)
            ->setParameter('confirmed', AppointmentStatus::CONFIRMED->value)
            ->setParameter('occurred', AppointmentStatus::OCCURRED->value)
            ->setParameter('date', $date)
            ->orderBy('statusOrder', 'ASC')
            ->addOrderBy('appointment.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countToProcess(\DateTimeImmutable $date): int
    {
        return (int) $this->createQueryBuilder('appointment')
            ->select('COUNT(appointment.id)')
            ->leftJoin('appointment.summary', 'summary')
            ->andWhere(
                'appointment.status = :requested
                OR (appointment.status = :confirmed AND appointment.endAt <= :date)
                OR (appointment.status = :occurred AND summary.id IS NULL)',
            )
            ->setParameter('requested', AppointmentStatus::REQUESTED->value)
            ->setParameter('confirmed', AppointmentStatus::CONFIRMED->value)
            ->setParameter('occurred', AppointmentStatus::OCCURRED->value)
            ->setParameter('date', $date)
            ->getQuery()
            ->getSingleScalarResult();
    }

    //    /**
    //     * @return Appointment[] Returns an array of Appointment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Appointment
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
