<?php

namespace App\Repository;

use App\Entity\HeroPlayer;
use App\Entity\HeroPlayerAbility;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<HeroPlayerAbility>
 *
 * @method HeroPlayerAbility|null find($id, $lockMode = null, $lockVersion = null)
 * @method HeroPlayerAbility|null findOneBy(array $criteria, array $orderBy = null)
 * @method HeroPlayerAbility[]    findAll()
 * @method HeroPlayerAbility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    public function getTwOmicron(HeroPlayer $heroPlayer)
    {
        return $this->createQueryBuilder('hpa')
            ->join('hpa.ability', 'a', 'WITH', 'hpa.ability = a and a.omicron_mode = 8')
            ->andWhere('hpa.heroPlayer = :heroPlayer')
            ->setParameter('heroPlayer', $heroPlayer)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return HeroPlayerAbility[] Returns an array of HeroPlayerAbility objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HeroPlayerAbility
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
