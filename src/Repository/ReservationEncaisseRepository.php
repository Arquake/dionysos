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
}
