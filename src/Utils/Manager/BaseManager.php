<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;

class BaseManager
{
    protected $repository;
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function getEntityManager()
    {
        return $this->entityManagerInterface;
    }

    public function setRepositoryByClass($class)
    {
        $this->repository = $this->entityManager->getRepository($class);
    }

    public function getRepository()
    {
        return $this->repository;
    }
}