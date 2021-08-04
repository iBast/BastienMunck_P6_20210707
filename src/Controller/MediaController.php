<?php

namespace App\Controller;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use App\Repository\TrickRepository;
use App\Manager\MediaManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaController extends AbstractController
{
    protected $em;
    protected $mediaRepository;
    protected $trickRepository;
    protected $mediaManager;

    public function __construct(EntityManagerInterface $em, MediaRepository $mediaRepository, TrickRepository $trickRepository, MediaManager  $mediaManager)
    {
        $this->em = $em;
        $this->mediaRepository = $mediaRepository;
        $this->trickRepository = $trickRepository;
        $this->mediaManager = $mediaManager;
    }

    #[Route('/video/delete/{id}', name: 'media_delete')]
    public function delete($id, Request $request): Response
    {
        $media = $this->mediaRepository->findOneBy(['id' => $id]);
        $submittedToken = $request->request->get('token');
        $slug = $media->getTrick()->getSlug();
        // 'delete-item' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-media', $submittedToken)) {

            $this->em->remove($media);
            $this->em->flush();
            $this->addFlash('success', "Video deleted");
            return $this->redirectToRoute('trick_edit', ['slug' => $slug]);
        }
        $this->addFlash('danger', "You can't delete this video");
        return $this->redirectToRoute('homepage');
    }

    #[Route('/video/add/{id}', name: 'video_add')]
    public function add($id, Request $request, EntityManagerInterface $em)
    {
        $video = new Media;
        $form = $this->createForm(MediaType::class, $video);
        $trick = $this->trickRepository->find($id);
        $slug = $trick->getSlug();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $link = $form->get('link')->getData();
            if ($this->mediaManager->verifyLink($link) == false) {
                $this->addFlash('danger', 'The video is not from youtube');
                return $this->redirectToRoute('video_add', ['id' => $trick->getId()]);
            }
            $path = $this->mediaManager->parseLink($link);
            $this->mediaManager->save($video, $trick, $path);
            return $this->redirectToRoute('trick_show', ['slug' => $slug]);
        }
        return $this->render('trick/addVideo.html.twig', [
            'slug' => $trick->getSlug(),
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    #[Route('/video/edit/{id}', name: 'video_edit')]
    public function edit($id, Request $request, EntityManagerInterface $em)
    {
        $video = $this->mediaRepository->find($id);
        $form = $this->createForm(MediaType::class, $video);
        $trick = $this->trickRepository->find($video->getTrick());
        $slug = $trick->getSlug();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $link = $form->get('link')->getData();
            if ($this->mediaManager->verifyLink($link) == false) {
                $this->addFlash('danger', 'The video is not from youtube');
                return $this->redirectToRoute('video_add', ['id' => $trick->getId()]);
            }
            $path = $this->mediaManager->parseLink($link);
            $this->mediaManager->save($video, $trick, $path);
            return $this->redirectToRoute('trick_show', ['slug' => $slug]);
        }

        return $this->render('trick/addVideo.html.twig', [
            'slug' => $trick->getSlug(),
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }
}
