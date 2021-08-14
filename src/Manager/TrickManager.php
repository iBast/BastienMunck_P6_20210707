<?php

namespace App\Manager;

use DateTime;
use DateTimeZone;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\EntityInterface;
use App\Service\PaginatorService;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;


class TrickManager extends AbstractManager
{
    protected $security;
    protected $em;
    protected $slugger;
    protected $commentRepository;
    protected $paginator;
    protected $trickRepository;
    protected $flashBag;
    protected $request;
    protected $router;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CommentRepository $commentRepository,
        PaginatorService $paginator,
        TrickRepository $trickRepository,
        FlashBagInterface $flashBag,
        RouterInterface $router
    ) {
        $this->security = $security;
        $this->slugger = $slugger;
        $this->paginator = $paginator;
        $this->commentRepository = $commentRepository;
        $this->trickRepository = $trickRepository;
        $this->flashBag = $flashBag;
        $this->router = $router;
        parent::__construct($em);
    }

    public function initialise(EntityInterface $entity)
    {
        /** @var Trick */
        $trick = $entity;

        $trick->setName($this->getUniqueName($trick));
        $trick->setSlug($this->slugger->slug($trick->getName()));
        if ($trick->getid() == null) {

            $trick->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setOwner($this->security->getUser())
                ->setMainPicture(null);
        } else {
            $trick->setUpdatedAt(new DateTime());
        }
    }

    public function saveComment(Comment $comment, Trick $trick)
    {
        $comment->setAuthor($this->security->getUser())
            ->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
            ->setTrick($trick);
        $this->em->persist($comment);
        $this->em->flush();
    }

    public function paginate($trickId, int $limit = 10, int $page = 1, $increment = 10)
    {
        $querry = $this->commentRepository->createQueryBuilder(CommentRepository::ALIAS)
            ->select(CommentRepository::ALIAS)
            ->where(CommentRepository::ALIAS . '.trick = ' . $trickId)
            ->orderBy(CommentRepository::ALIAS . '.createdAt', 'DESC');

        return $this->paginator->render('comments', $querry, $limit, $page, $increment);
    }

    public function getUniqueName($entity)
    {
        /** @var Trick */
        $trick = $entity;
        $name = $trick->getName();

        $count = 1;
        $nameUnique = false;
        while ($nameUnique == false) {
            if ($this->trickRepository->findCountForName($name, $trick->getId())) {
                $name .= ' ' . $count;
                $count++;
            } else {
                $nameUnique = true;
            }
        }
        return $name;
    }

    public function getUniqueSlug($entity)
    {
        /** @var Trick */
        $trick = $entity;
        $slug = $trick->getSlug();

        $count = 1;
        $slugUnique = false;
        while ($slugUnique == false) {
            if ($this->trickRepository->findCountForName($slug, $trick->getId())) {
                $slug .= $count;
                $count++;
            } else {
                $slugUnique = true;
            }
        }
        return $slug;
    }
}
