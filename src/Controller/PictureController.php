<?php

namespace App\Controller;

use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/picture/delete/{id}', name: 'picture_delete')]
    public function delete($id, Request $request, PictureRepository $pictureRepository): Response
    {
        $picture = $pictureRepository->findOneBy(['id' => $id]);
        $submittedToken = $request->request->get('token');
        $slug = $picture->getTrick()->getSlug();
        // 'delete-item' is the same value used in the template to generate the token
        if ($this->isCsrfTokenValid('delete-picture', $submittedToken)) {
            if ($picture->getId() === $picture->getTrick()->getMainPicture()->getId()) {
                $this->addFlash('danger', "This picture is the main picture, before deleting it you must change the main picture");
                return $this->redirectToRoute('trick_edit', [
                    'slug' => $slug
                ]);
            }
            $this->em->remove($picture);
            $this->em->flush();
            $this->addFlash('success', "Picture deleted");
            return $this->redirectToRoute('trick_edit', [
                'slug' => $slug
            ]);
        }
        $this->addFlash('danger', "You can't delete this Picture");
        return $this->redirectToRoute('homepage');
    }
}
