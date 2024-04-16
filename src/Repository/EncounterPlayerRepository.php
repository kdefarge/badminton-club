<?php

namespace App\Repository;

use App\Entity\EncounterPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EncounterPlayer>
 *
 * @method EncounterPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method EncounterPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method EncounterPlayer[]    findAll()
 * @method EncounterPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncounterPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EncounterPlayer::class);
    }

    //    /**
    //     * @return EncounterPlayer[] Returns an array of EncounterPlayer objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?EncounterPlayer
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
