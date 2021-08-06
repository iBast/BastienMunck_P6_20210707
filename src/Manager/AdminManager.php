<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use App\Service\PaginatorService;

class AdminManager extends AbstractManager
{
    protected $trickRepository;
    protected $userRepository;
    protected $commentRepository;
    protected $paginator;

    public function __construct(TrickRepository $trickRepository, UserRepository $userRepository, CommentRepository $commentRepository, PaginatorService $paginator)
    {
        $this->trickRepository = $trickRepository;
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
        $this->paginator = $paginator;
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

    public function paginateComments()
    {
        $querry = $this->commentRepository->createQueryBuilder(CommentRepository::ALIAS)
            ->select(CommentRepository::ALIAS)
            ->orderBy(CommentRepository::ALIAS . '.createdAt', 'DESC');
        return $this->paginator->render('comments', $querry, 100);
    }

    public function paginateUsers()
    {
        $querry = $this->userRepository->createQueryBuilder('u')
            ->select('u')
            ->orderBy('u' . '.nickname', 'ASC');
        return $this->paginator->render('users', $querry, 100);
    }
}
