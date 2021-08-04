<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PictureManager extends AbstractManager
{

    protected $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->security = $security;
        parent::__construct($manager);
    }

    public function initialise(EntityInterface $entity): void
    {
        /** @var Picture */
        $picture = $entity;
        $picture->setAddedBy($this->security->getUser())
            ->setMainToTrick(null);
    }
}
