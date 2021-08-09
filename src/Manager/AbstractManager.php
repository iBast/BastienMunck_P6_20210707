<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager implements ManagerInterface
{

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function save(EntityInterface $entity): void
    {
        $this->initialise($entity);

        $this->em->persist($entity);
        $this->em->flush();
    }

    public function remove(EntityInterface $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function error($type, $message, $redirection, $options = null)
    {
        $type = $type;
        $message = $message;
        $redirection = $redirection;
        $options = $options;
    }
}
