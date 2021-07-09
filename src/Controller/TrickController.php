<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'trick_show', priority: -1)]
    public function show(
        $slug,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        CommentRepository $commentRepository,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $formView = $form->createView();
        $trick = $trickRepository->findOneBy(['slug' => $slug]);
        $medias = $mediaRepository->findBy(['trick' => $trick->getId()]);
        $comments = $commentRepository->findBy(['trick' => $trick->getId()], ['createdAt' => 'DESC']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                ->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
                ->setTrick($trick);
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', "Your comment has been registred");

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'medias' => $medias,
            'mainPic' => $mediaRepository->findOneBy(['type' => 'picture', 'trick' => $trick->getId()]),
            'comments' => $comments,
            'formView' => $formView
        ]);
    }
    #[Route('/trick/new', name: 'trick_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $trick = new Trick;
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setOwner($this->getUser())
                ->setSlug($slugger->slug($trick->getName()));
            $em->persist($trick);
            $em->flush();

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
}
