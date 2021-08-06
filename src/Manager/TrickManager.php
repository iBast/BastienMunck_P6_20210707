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
    protected $manager;
    protected $slugger;
    protected $commentRepository;
    protected $paginator;
    protected $trickRepository;
    protected $flashBag;
    protected $request;
    protected $router;

    public function __construct(
        Security $security,
        EntityManagerInterface $manager,
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
        parent::__construct($manager);
    }

    public function initialise(EntityInterface $entity)
    {
        /** @var Trick */
        $trick = $entity;

        if ($entity->getid() == null) {
            $nameExist = $this->trickRepository->findOneBy(['name' => $trick->getName()]);
            $slugExist = $this->trickRepository->findOneBy(['slug' => $trick->getSlug()]);

            $trick->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setOwner($this->security->getUser())
                ->setSlug($this->slugger->slug($trick->getName()))
                ->setMainPicture(null);

            if ($nameExist->getName() === $trick->getName()) {
                $this->flashBag->add('danger', 'This name already exist, please choose an other name');
                $response = new RedirectResponse($this->router->generate('trick_new'));
                return $response->send();
            }
            if ($slugExist->getSlug() !== $trick->getSlug()) {
                $this->flashBag->add('danger', 'This slug already exist, please try again later');
                $response = new RedirectResponse('trick_new');
                return $response->send();
            }
        } else {
            $nameExist = $this->trickRepository->findOneBy(['name' => $trick->getName()]);
            $slugExist = $this->trickRepository->findOneBy(['slug' => $trick->getSlug()]);
            if ($nameExist->getId() != $trick->getId()  && $nameExist->getName() === $trick->getName()) {
                $this->flashBag->add('danger', 'This name already exist, please choose an other name');
                $response = new RedirectResponse($this->router->generate('trick_edit', ['slug' => $trick->getSlug()]));
                return $response->send();
            }
            if ($nameExist->getId() != $trick->getId()  && $slugExist->getSlug() !== $trick->getSlug()) {
                $this->flashBag->add('danger', 'This slug already exist, please try again later');
                $response = new RedirectResponse($this->router->generate('trick_edit', ['slug' => $trick->getSlug()]));
                return $response->send();
            }
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
