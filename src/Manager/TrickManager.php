<?php

namespace App\Manager;

use DateTime;
use DateTimeZone;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\EntityInterface;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickManager extends AbstractManager
{
    protected $security;
    protected $manager;
    protected $slugger;
    protected $commentRepository;
    protected $paginator;

    public function __construct(
        Security $security,
        EntityManagerInterface $manager,
        SluggerInterface $slugger,
        CommentRepository $commentRepository,
        PaginatorService $paginator
    ) {
        $this->security = $security;
        $this->slugger = $slugger;
        $this->paginator = $paginator;
        $this->commentRepository = $commentRepository;
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

    public function paginate($trickId, int $limit = 10, int $page = 1, $increment = 10)
    {
        $querry = $this->commentRepository->createQueryBuilder(CommentRepository::ALIAS)
            ->select(CommentRepository::ALIAS)
            ->where(CommentRepository::ALIAS . '.trick = ' . $trickId)
            ->orderBy(CommentRepository::ALIAS . '.createdAt', 'DESC');

        return $this->paginator->render('comments', $querry, $limit, $page, $increment);
    }
}
