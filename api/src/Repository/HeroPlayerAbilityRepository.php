<?php

namespace App\Repository;

use App\Entity\HeroPlayer;
use App\Entity\HeroPlayerAbility;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<HeroPlayerAbility>
*/
class HeroPlayerAbilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HeroPlayerAbility::class);
    }

    public function save(HeroPlayerAbility $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HeroPlayerAbility $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTwOmicron(HeroPlayer $heroPlayer): mixed
    {
        return $this->createQueryBuilder('hpa')
            ->join('hpa.ability', 'a', 'WITH', 'hpa.ability = a and a.omicron_mode = 8')
            ->andWhere('hpa.heroPlayer = :heroPlayer')
            ->setParameter('heroPlayer', $heroPlayer)
            ->getQuery()
            ->getResult();
    }
}
