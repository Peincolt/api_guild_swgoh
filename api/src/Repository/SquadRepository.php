<?php

namespace App\Repository;

use App\Entity\Guild;
use App\Entity\Squad;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Squad>
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

    public function getGuildSquad(Guild $guild, string $type = null): mixed
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

    /**
     * @param array<mixed> $dataForm
     */
    public function getGuildSquadByFilter(Guild $guild, array $dataForm, bool $hydratation = true): mixed
    {
        $query = $this->createQueryBuilder('s')
            ->andWhere(':guild MEMBER OF s.guilds')
            ->setParameter(':guild', $guild);
        foreach ($dataForm as $property => $value) {
            if ($property === 'name') {
                $query->andWhere('s.name LIKE :like')
                    ->setParameter(':like', '%'.$value.'%');
            } else {
                $query->andWhere("s.$property = :$property")
                    ->setParameter(":$property", $value);
            }
        }

        $query = $query->getQuery();
        
        if (!empty($hydratation)) {
            return $query->getResult(Query::HYDRATE_ARRAY);
        } else {
            return $query->getResult();
        }
    }
}
