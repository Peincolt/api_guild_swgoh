<?php

namespace App\Repository;

use App\Entity\Guild;
use App\Entity\Squad;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Squad>
 *
 * @method Squad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Squad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Squad[]    findAll()
 * @method Squad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SquadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Squad::class);
    }

    public function save(Squad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Squad $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getGuildSquad(Guild $guild, string $type = null)
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere(':guild MEMBER OF s.guilds')
            ->setParameter(':guild', $guild);
        if (!empty($type)) {
            $query->andWhere('s.used_for = :usedFor')
                ->setParameter(':usedFor', $type);
        }
        return $query->getQuery()
            ->getResult();

    }
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;

//    /**
//     * @return Squad[] Returns an array of Squad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Squad
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
