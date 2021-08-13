<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Manager\AbstractManager;
use App\Service\PaginatorService;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdminManager extends AbstractManager
{
    protected $trickRepository;
    protected $userRepository;
    protected $commentRepository;
    protected $paginator;
    protected $em;

    public function __construct(TrickRepository $trickRepository, UserRepository $userRepository, CommentRepository $commentRepository, PaginatorService $paginator, EntityManagerInterface $em)
    {
        $this->trickRepository = $trickRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
        parent::__construct($em);
    }

    public function initialise(EntityInterface $entity)
    {
        //interface
    }

    public function getCounts()
    {
        $userCount = $this->userRepository->count([]);
        $trickCount = $this->trickRepository->count([]);
        $commentCount = $this->commentRepository->count([]);

        return array('users' => $userCount, 'tricks' => $trickCount, 'comments' => $commentCount);
    }

    public function paginateComments($limit, $page)
    {
        $querry = $this->commentRepository->createQueryBuilder(CommentRepository::ALIAS)
            ->select(CommentRepository::ALIAS)
            ->orderBy(CommentRepository::ALIAS . '.createdAt', 'DESC');
        return $this->paginator->render('comments', $querry, $limit, $page);
    }

    public function paginateUsers($limit, $page)
    {
        $querry = $this->userRepository->createQueryBuilder('u')
            ->select('u')
            ->orderBy('u' . '.nickname', 'ASC');
        return $this->paginator->render('users', $querry, $limit, $page);
    }

    public function delete($controller, $id)
    {
        $controllerRepository = $controller . 'Repository';
        $item = $this->$controllerRepository->find($id);
        $this->remove($item);
    }
}
