<?php

namespace App\Controller;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Manager\PictureManager;
use App\Repository\PictureRepository;
use App\Repository\TrickRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PictureController extends AbstractController
{
    protected $pictureManager;
    protected $trickRepository;

    public function __construct(PictureManager $pictureManager, TrickRepository $trickRepository)
    {
        $this->pictureManager = $pictureManager;
        $this->trickRepository = $trickRepository;
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
                return $this->redirectToRoute('trick_edit', ['slug' => $slug]);
            }
            $this->pictureManager->remove($picture);
            $this->addFlash('success', "Picture deleted");
            return $this->redirectToRoute('trick_edit', ['slug' => $slug]);
        }
        $this->addFlash('danger', "You can't delete this Picture");
        return $this->redirectToRoute('homepage');
    }

    #[Route('/picture/add/{id}', name: 'picture_add')]
    public function add($id, Request $request, UploadFileService $uploadFileService, EntityManagerInterface $em)
    {
        $picture = new Picture;
        $form = $this->createForm(PictureType::class);
        $trick = $this->trickRepository->find($id);
        $slug = $trick->getSlug();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $fileName = $uploadFileService->upload($file);
            $picture->setTrick($trick)->setPath($fileName);
            $this->pictureManager->save($picture);
            return $this->redirectToRoute('trick_show', ['slug' => $slug]);;
        }

        return $this->render('trick/addPicture.html.twig', [
            'slug' => $trick->getSlug(),
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }
}
