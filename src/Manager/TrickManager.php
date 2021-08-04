<?php

namespace App\Manager;

use DateTime;
use DateTimeZone;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickManager extends AbstractManager
{
    protected $security;
    protected $manager;
    protected $slugger;

    public function __construct(Security $security, EntityManagerInterface $manager, SluggerInterface $slugger)
    {
        $this->security = $security;
        $this->slugger = $slugger;
        parent::__construct($manager);
    }

    public function initialise(EntityInterface $entity): void
    {
        /** @var Trick */
        $trick = $entity;
        if ($entity->getid() == null) {
            $trick->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setOwner($this->security->getUser())
                ->setSlug($this->slugger->slug($trick->getName()))
                ->setMainPicture(null);
        } else {
            $trick->setUpdatedAt(new DateTime())
                ->setSlug($this->slugger->slug($trick->getName()));
        }
    }

    public function saveComment(Comment $comment, Trick $trick)
    {
        $comment->setAuthor($this->security->getUser())
            ->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setTrick($trick);
        $this->manager->persist($comment);
        $this->manager->flush();
    }
}
