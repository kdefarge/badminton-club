<?php

namespace App\Repository;

use App\Entity\Score;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScoreRepository>
 *
 * @method ScoreRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScoreRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScoreRepository[]    findAll()
 * @method ScoreRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Score::class);
    }
}
