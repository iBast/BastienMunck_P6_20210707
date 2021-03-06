<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Manager\PictureManager;
use App\Service\UploadFileService;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    #[IsGranted('ROLE_USER')]
    public function delete(Picture $picture, Request $request, PictureRepository $pictureRepository): Response
    {
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
    #[IsGranted('ROLE_USER')]
    public function add(Trick $trick, Request $request, UploadFileService $uploadFileService)
    {
        $picture = new Picture;
        $form = $this->createForm(PictureType::class);
        $slug = $trick->getSlug();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            $fileName = $uploadFileService->upload($file);
            $picture->setTrick($trick)->setPath($fileName);
            if ($trick->getMainPicture() === null) {
                $trick->setMainPicture($picture);
            }
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
