<?php

namespace App\Manager;

use App\Entity\EntityInterface;

interface ManagerInterface
{
    public function initialise(EntityInterface $entity);

    public function save(EntityInterface $entity): void;

    public function remove(EntityInterface $entity): void;
}
