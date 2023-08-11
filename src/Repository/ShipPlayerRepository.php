<?php

namespace App\Repository;

use App\Entity\ShipPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ShipPlayer>
 *
 * @method ShipPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShipPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShipPlayer[]    findAll()
 * @method ShipPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShipPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipPlayer::class);
    }

    public function save(ShipPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ShipPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
