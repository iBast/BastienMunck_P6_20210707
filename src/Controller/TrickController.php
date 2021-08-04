<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Manager\TrickManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    protected $trickRepository;
    protected $trickManager;

    public function __construct(
        TrickRepository $trickRepository,
        TrickManager $trickManager
    ) {
        $this->trickRepository = $trickRepository;
        $this->trickManager = $trickManager;
    }

    #[Route('/trick/{slug}', name: 'trick_show', priority: -1)]
    public function show($slug, Request $request): Response
    {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $formView = $form->createView();
        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            $this->addFlash('danger', 'This trick doesn\'t exist');
            return $this->redirectToRoute('homepage');
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->saveComment($comment, $trick);
            $this->addFlash('success', 'Your comment has been registred');
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'formView' => $formView
        ]);
    }

    #[Route('/trick/new', name: 'trick_new')]
    public function new(Request $request)
    {
        $trick = new Trick;

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->save($trick);
            $this->addFlash('success', "Your trick has been registred");
            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug()
            ]);
        }

        $formView = $form->createView();
        return $this->render('trick/new.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/edit/{slug}', name: 'trick_edit')]
    public function edit($slug, Request $request)
    {
        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->trickManager->save($trick);
            $this->addFlash('success', "Your trick {$trick->getName()} has been updated");
            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        $formView = $form->createView();
        return $this->render('trick/edit.html.twig', [
            'slug' => $slug,
            'trick' => $trick,
            'formView' => $formView
        ]);
    }

    #[Route('/delete/{id}', name: 'trick_delete')]
    public function delete($id, Request $request)
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);
        $submittedToken = $request->request->get('token');

        // 'delete-item' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-trick', $submittedToken)) {
            $this->trickManager->remove($trick);
            $this->addFlash('success', "Trick deleted");
            return $this->redirectToRoute('homepage');
        }
        $this->addFlash('danger', "You can't delete this trick");
        return $this->redirectToRoute('homepage');
    }
}
