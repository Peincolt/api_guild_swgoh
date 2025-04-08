<?php

namespace App\Utils\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

class BaseManager
{
    protected ObjectRepository $repository;
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param class-string<object> $class
     */
    public function setRepositoryByClass($class): void
    {
        $this->repository = $this->entityManager->getRepository($class);
    }

    public function getRepository(): ObjectRepository
    {
        return $this->repository;
    }
}