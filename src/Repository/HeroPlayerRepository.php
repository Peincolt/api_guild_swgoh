<?php

namespace App\Repository;

use App\Entity\HeroPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HeroPlayer>
 *
 * @method HeroPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeroPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeroPlayer[]    findAll()
 * @method HeroPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeroPlayer::class);
    }

    public function save(HeroPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HeroPlayer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
