<?php

namespace App\Controller;

use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/video/delete/{id}', name: 'media_delete')]
    public function delete($id, Request $request, MediaRepository $mediaRepository): Response
    {
        $media = $mediaRepository->findOneBy(['id' => $id]);
        $submittedToken = $request->request->get('token');
        $slug = $media->getTrick()->getSlug();
        // 'delete-item' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-media', $submittedToken)) {

            $this->em->remove($media);
            $this->em->flush();
            $this->addFlash('success', "Video deleted");
            return $this->redirectToRoute('trick_edit', [
                'slug' => $slug
            ]);
        }
        $this->addFlash('danger', "You can't delete this video");
        return $this->redirectToRoute('homepage');
    }
}
