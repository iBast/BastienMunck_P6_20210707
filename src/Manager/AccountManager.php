<?php

namespace App\Manager;

use App\Entity\User;
use App\Entity\EntityInterface;
use App\Security\EmailVerifier;
use App\Manager\AbstractManager;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class AccountManager extends AbstractManager
{
    protected $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    public function initialise(EntityInterface $entity): void
    {
        //interface
    }

    public function reinit(User $user)
    {
        $user->setIsVerified(false)->setRoles([]);
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('hello@bastienmunck.fr', 'SnowTricks'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
