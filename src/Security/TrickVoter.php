<?php

namespace App\Security;

use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TrickVoter extends Voter
{
    const READ = 'read';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const CREATE = 'create';

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::READ, self::UPDATE, self::DELETE, self::CREATE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if ($attribute != self::CREATE) {
            if (null !== $subject and !$subject instanceof Trick) {
                return false;
            }
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        if (!$this->user instanceof User) {
            return false;
        }

        /** @var Trick $trick */
        $trick = $subject;
        switch ($attribute) {
            case self::READ:
                return $this->canRead($trick, $this->user);
            case self::UPDATE:
                return $this->canUpdate($trick, $this->user);
            case self::DELETE:
                return $this->canDelete($trick, $this->user);
            case self::CREATE:
                return $this->canCreate($this->user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    public function canRead(Trick $trick, User $user)
    {
        return true;
    }

    public function canUpdate(Trick $trick, User $user)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_USER', $user->getRoles()) && $trick->getOwner() == $user) {

            return true;
        }
        return false;
    }

    public function canDelete(Trick $trick, User $user)
    {
        return $this->canUpdate($trick, $user);
    }

    public function canCreate(User $user)
    {
        if (in_array('ROLE_USER', $user->getRoles())) {
            return true;
        }
        return false;
    }
}
