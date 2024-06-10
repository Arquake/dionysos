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

    //    /**
    //     * @return NonReservationEncaisse[] Returns an array of NonReservationEncaisse objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('n.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?NonReservationEncaisse
    //    {
    //        return $this->createQueryBuilder('n')
    //            ->andWhere('n.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
