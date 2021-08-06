<?php

namespace App\Controller;

use App\Manager\AdminManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    protected $manager;

    public function __construct(AdminManager $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $counts = $this->manager->getCounts();

        return $this->render('admin/index.html.twig', [
            'counts' => $counts
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function users(): Response
    {
        $options = $this->manager->paginateUsers();

        return $this->render('admin/users.html.twig', $options);
    }

    #[Route('/admin/comments', name: 'admin_comments')]
    public function comments(): Response
    {
        $options = $this->manager->paginateComments();

        return $this->render('admin/comments.html.twig', $options);
    }
}
