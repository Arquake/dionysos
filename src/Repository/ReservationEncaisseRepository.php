<?php

namespace App\Repository;

use App\Entity\ReservationEncaisse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReservationEncaisse>
 *
 * @method ReservationEncaisse|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationEncaisse|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationEncaisse[]    findAll()
 * @method ReservationEncaisse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationEncaisseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationEncaisse::class);
    }

    //    /**
    //     * @return ReservationEncaisse[] Returns an array of ReservationEncaisse objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ReservationEncaisse
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function reservationIDAlreadyExist(int $idReservation): bool {
        return $this->createQueryBuilder('r')
                ->andWhere('r.idReservation = :idReservation')
                ->setParameter('idReservation', $idReservation)
                ->getQuery()
                ->getOneOrNullResult() !== null;
    }

    public function reservationOrderedByDate() {
        return $this->createQueryBuilder('r')
            ->OrderBy('r.dateReservation', 'ASC')
            ->getQuery()->execute();
    }

    public function findReservationsByMonth(int $year, int $month): array
    {
        $startDate = new \DateTime("$year-$month-01");
        $endDate = (clone $startDate)->modify('last day of this month')->setTime(23, 59, 59);

        $qb = $this->createQueryBuilder('r')
            ->where('r.date >= :startDate')
            ->andWhere('r.date <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        return $qb->getQuery()->getResult();
    }

    public function averageMorningCovers(): array {
        $qb = $this->createQueryBuilder('r')
            ->where('r.date BETWEEN :startDate AND :endDate')
            ->andWhere('r.horraire BETWEEN :startTime AND :endTime')
            ->setParameter('startDate', (new \DateTime())->modify('-27 days'))
            ->setParameter('endDate', new \DateTime())
            ->setParameter('startTime', new \DateTime('12:00:00'))
            ->setParameter('endTime', new \DateTime('16:00:00'))
            ->orderBy('r.date', 'ASC');

        return $this->calculateAverageCovers($qb);
    }

    public function averageEveningCovers(): array {
        $qb = $this->createQueryBuilder('r')
            ->where('r.date BETWEEN :startDate AND :endDate')
            ->andWhere('r.horraire BETWEEN :startTime AND :endTime')
            ->setParameter('startDate', (new \DateTime())->modify('-27 days'))
            ->setParameter('endDate', new \DateTime())
            ->setParameter('startTime', new \DateTime('19:00:00'))
            ->setParameter('endTime', new \DateTime('23:30:00'))
            ->orderBy('r.date', 'ASC');

        return $this->calculateAverageCovers($qb);
    }

    private function calculateAverageCovers($qb):array{
        $totalCovers = [
            'mon' => 0,
            'tue' => 0,
            'wed' => 0,
            'thu' => 0,
            'fri' => 0,
            'sat' => 0,
            'sun' => 0
        ];

        foreach ($qb->getQuery()->getResult() as $reservation) {
            $date = $reservation->getDate();
            $dayOfWeek = strtolower($date->format('D')); // 'D' gives a three-letter abbreviation of the day

            // Assuming $reservation->getCovers() returns the number of covers
            $covers = $reservation->getCouverts();

            $totalCovers[$dayOfWeek] += $covers;
        }

        $results = [];

        foreach ($totalCovers as $day => $covers) {
            $results[] = $covers / 4;
        }

        return $results;
    }
}
