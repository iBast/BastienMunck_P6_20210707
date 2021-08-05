<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Service\PaginatorService;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;

class HomeManager extends AbstractManager
{
    protected $paginator;
    protected $trickRepository;

    public function __construct(PaginatorService $paginator, TrickRepository $trickRepository)
    {
        $this->paginator = $paginator;
        $this->trickRepository = $trickRepository;
    }

    public function initialise(EntityInterface $entity): void
    {
        //ManagerInterface
    }

    public function paginate(int $limit = 10, int $page = 1, $increment = 10)
    {
        $querry = $this->trickRepository->createQueryBuilder(TrickRepository::ALIAS)->select(
            TrickRepository::ALIAS,
            PictureRepository::ALIAS
        )
            ->leftJoin(TrickRepository::ALIAS . '.mainPicture', PictureRepository::ALIAS)
            ->orderBy(TrickRepository::ALIAS . '.name', 'ASC');

        return $this->paginator->render('tricks', $querry, $limit, $page, $increment);
    }
}
