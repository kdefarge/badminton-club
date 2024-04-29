<?php

namespace App\Repository;

use App\Entity\Encounter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Encounter>
 *
 * @method Encounter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Encounter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Encounter[]    findAll()
 * @method Encounter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncounterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Encounter::class);
    }

    public function findAllJoined(): array
    {
        return $this->createQueryBuilder('e')
            ->select(['e', 'ep', 'p', 's'])
            ->leftJoin('e.encounterPlayers', 'ep')
            ->leftJoin('ep.player', 'p')
            ->leftJoin('e.scores', 's')
            ->orderBy('e.id', 'DESC')
            ->addOrderBy('s.number', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneJoinedByID($id): ?Encounter
    {
        return $this->createQueryBuilder('e')
            ->select(['e', 'ep', 'p', 's'])
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->leftJoin('e.encounterPlayers', 'ep')
            ->leftJoin('ep.player', 'p')
            ->leftJoin('e.scores', 's')
            ->orderBy('s.number', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
