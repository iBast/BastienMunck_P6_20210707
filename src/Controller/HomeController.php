<?php

namespace App\Controller;


use App\Manager\HomeManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    protected $manager;

    public function __construct(HomeManager $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/limit/{limit}', name: 'homepage_with_limit', methods: ['GET'])]
    #[Route('/', name: 'homepage')]
    public function index($limit = 8)
    {
        $options = $this->manager->paginate($limit, 1, 8);
        if ($options['page'] > $options['lastPageNb']) {
            return $this->redirectToRoute('homepage');
        }
        return $this->render('home/index.html.twig', $options);
    }
}
