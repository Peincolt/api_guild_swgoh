<?php

namespace App\Repository;

use App\Entity\SquadUnit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SquadUnit>
 *
 * @method SquadUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method SquadUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method SquadUnit[]    findAll()
 * @method SquadUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SquadUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SquadUnit::class);
    }

    public function save(SquadUnit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SquadUnit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
