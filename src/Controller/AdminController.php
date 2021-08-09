<?php

namespace App\Controller;

use App\Manager\AdminManager;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    protected $adminManager;

    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $counts = $this->adminManager->getCounts();

        return $this->render('admin/index.html.twig', [
            'counts' => $counts
        ]);
    }

    #[Route('/admin/users', name: 'admin_user')]
    #[Route('/admin/users/page/{page}', name: 'admin_user_page', methods: ['GET'])]
    public function users($limit = 10, $page = 1): Response
    {
        $options = $this->adminManager->paginateUsers($limit, $page);

        return $this->render('admin/users.html.twig', $options);
    }

    #[Route('/admin/comments', name: 'admin_comment')]
    #[Route('/admin/comments/page/{page}', name: 'admin_comment_page', methods: ['GET'])]
    public function comments($limit = 10, $page = 1): Response
    {
        $options = $this->adminManager->paginateComments($limit, $page);

        return $this->render('admin/comments.html.twig', $options);
    }

    #[Route('/admin/delete/{controller}/{id}', name: 'admin_delete')]
    public function delete($controller, $id)
    {
        $this->adminManager->delete($controller, $id);
        $this->addFlash('success', 'The item has been deleted');
        return $this->redirectToRoute('admin_comment');
    }
}
