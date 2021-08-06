<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Manager\AccountManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    protected $accountManager;
    protected $em;

    public function __construct(AccountManager $accountManager, EntityManagerInterface $em)
    {
        $this->accountManager = $accountManager;
        $this->em = $em;
    }

    #[Route('/account', name: 'account')]
    public function index(Request $request): Response
    {
        /** @var User */
        $user = $this->getUser();
        $email = $user->getEmail();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->getData()->getEmail() !==  $email) {
                $this->accountManager->reinit($user);
            }

            $this->em->flush();
            $this->addFlash('success', 'Your account have been  updated');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('account/index.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView()
        ]);
    }
}
