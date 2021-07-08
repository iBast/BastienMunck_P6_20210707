<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'trick_show')]
    public function show($slug, TrickRepository $trickRepository, MediaRepository $mediaRepository, CommentRepository $commentRepository): Response
    {
        $trick = $trickRepository->findOneBy(['slug' => $slug]);
        $medias = $mediaRepository->findBy(['trick' => $trick->getId()]);
        $comments = $commentRepository->findBy(['trick' => $trick->getId()], ['createdAt' => 'DESC']);

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'medias' => $medias,
            'mainPic' => $mediaRepository->findOneBy(['type' => 'picture', 'trick' => $trick->getId()]),
            'comments' => $comments
        ]);
    }
}
