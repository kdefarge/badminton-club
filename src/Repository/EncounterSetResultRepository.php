<?php

namespace App\Repository;

use App\Entity\EncounterSetResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EncounterSetResult>
 *
 * @method EncounterSetResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method EncounterSetResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method EncounterSetResult[]    findAll()
 * @method EncounterSetResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EncounterSetResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EncounterSetResult::class);
    }
}
