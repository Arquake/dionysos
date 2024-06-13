<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }
    /**
     * @return Reservation[]
     */
    public function findByDateAfter(string $date): array{

        return $this->createQueryBuilder('r')
        ->where('r.date >= :date')
        ->orderBy('r.date', 'ASC')
        ->setParameter('date', $date)
        ->getQuery()
        ->getResult();
    }

    public function findAtLeastOneDayBeforeToday(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return $this->createQueryBuilder('e')
            ->andWhere('e.date < :today')
            ->setParameter('today', $today)
            ->orderBy('e.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
