<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\MediaType;
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
    protected $trickRepository;
    protected $mediaRepository;
    protected $commentRepository;
    protected $em;

    public function __construct(
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        CommentRepository $commentRepository,
        EntityManagerInterface $em,
    ) {
        $this->trickRepository = $trickRepository;
        $this->commentRepository = $commentRepository;
        $this->mediaRepository = $mediaRepository;
        $this->em = $em;
    }

    #[Route('/trick/{slug}', name: 'trick_show', priority: -1)]
    public function show($slug, Request $request): Response
    {
        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $formView = $form->createView();
        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);
        $medias = $this->mediaRepository->findBy(['trick' => $trick->getId()]);
        $comments = $this->commentRepository->findBy(['trick' => $trick->getId()], ['createdAt' => 'DESC']);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser())
                ->setCreatedAt(new DateTime('now', new DateTimeZone('Europe/Paris')))
                ->setTrick($trick);
            $this->em->persist($comment);
            $this->em->flush();

            $this->addFlash('success', "Your comment has been registred");

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'medias' => $medias,
            'comments' => $comments,
            'formView' => $formView
        ]);
    }

    #[Route('/trick/new', name: 'trick_new')]
    public function new(SluggerInterface $slugger, Request $request)
    {
        $trick = new Trick;
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
                ->setOwner($this->getUser())
                ->setSlug($slugger->slug($trick->getName()));
            $this->em->persist($trick);
            $this->em->flush();

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
    public function edit($slug)
    {
        $trick = $this->trickRepository->findOneBy(['slug' => $slug]);
        $medias = $this->mediaRepository->findBy(['trick' => $trick->getId()]);
        $form = $this->createForm(TrickType::class, $trick);
        $formView = $form->createView();
        $mediaForm = $this->createForm(MediaType::class);
        $mediaView = $mediaForm->createView();

        return $this->render('trick/edit.html.twig', [
            'slug' => $slug,
            'trick' => $trick,
            'medias' => $medias,
            'formView' => $formView,
            'mediaView' => $mediaView
        ]);
    }

    #[Route('/delete/{{id}}', name: 'trick_delete')]
    public function delete($id)
    {
        $trick = $this->trickRepository->findOneBy(['id' => $id]);
    }
}
