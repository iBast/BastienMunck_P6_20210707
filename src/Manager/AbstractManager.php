<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager implements ManagerInterface
{

    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function save(EntityInterface $entity): void
    {
        $this->initialise($entity);

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    public function remove(EntityInterface $entity): void
    {
        $this->manager->remove($entity);
        $this->manager->flush();
    }
}
