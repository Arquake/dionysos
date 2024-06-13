<?php

namespace App\Repository;

use App\Entity\NonReservationEncaisse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NonReservationEncaisse>
 *
 * @method NonReservationEncaisse|null find($id, $lockMode = null, $lockVersion = null)
 * @method NonReservationEncaisse|null findOneBy(array $criteria, array $orderBy = null)
 * @method NonReservationEncaisse[]    findAll()
 * @method NonReservationEncaisse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NonReservationEncaisseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NonReservationEncaisse::class);
    }

    public function orderByDate() {
        return $this->createQueryBuilder('r')
            ->OrderBy('r.date', 'ASC')
            ->getQuery()->execute();
    }

    public function findEncaissementsByMonth(int $year, int $month): array
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
            ->andWhere('r.midi = true')
            ->setParameter('startDate', (new \DateTime())->modify('-27 days'))
            ->setParameter('endDate', new \DateTime())
            ->orderBy('r.date', 'ASC');

        return $this->calculateAverageCovers($qb);
    }

    public function averageEveningCovers(): array {
        $qb = $this->createQueryBuilder('r')
            ->where('r.date BETWEEN :startDate AND :endDate')
            ->andWhere('r.midi = false')
            ->setParameter('startDate', (new \DateTime())->modify('-27 days'))
            ->setParameter('endDate', new \DateTime())
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

    public function findOlderThanFourYears(): array
    {
        $fourYearsAgo = new \DateTime();
        $fourYearsAgo->modify('-4 years');
        $fourYearsAgo->setTime(0, 0, 0);

        return $this->createQueryBuilder('e')
            ->andWhere('e.date < :fourYearsAgo')
            ->setParameter('fourYearsAgo', $fourYearsAgo)
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
