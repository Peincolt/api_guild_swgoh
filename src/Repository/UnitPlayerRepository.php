<?php

namespace App\Repository;

use App\Entity\UnitPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UnitPlayer>
 *
 * @method UnitPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method UnitPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method UnitPlayer[]    findAll()
 * @method UnitPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UnitPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnitPlayer::class);
    }

    public function save(UnitPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UnitPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
