<?php

namespace App\Repository;

use App\Entity\Ability;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ability>
 *
 * @method Ability|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ability|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ability[]    findAll()
 * @method Ability[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AbilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ability::class);
    }

    public function save(Ability $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ability $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
