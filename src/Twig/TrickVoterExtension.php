<?php

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFilter;
use App\Entity\Trick;
use App\Security\TrickVoter;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Security\Core\Security;

class TrickVoterExtension extends AbstractExtension
{
    /**
     * @var TrickVoter
     */
    private $trickVoter;

    /**
     * @var User
     */
    protected $user;


    /**
     * TrickExtension constructor.
     * @param CurrentUser $user
     * @param TrickDtoRepository $trickDtoRepository
     * @param TrickVoter $trickVoter
     */
    public function __construct(
        Security $security,
        TrickVoter $trickVoter

    ) {
        $this->user = $security->getUser();

        $this->trickVoter = $trickVoter;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('trickCanRead', [$this, 'trickCanRead']),
            new TwigFilter('trickCanUpdate', [$this, 'trickCanUpdate']),
            new TwigFilter('trickCanDelete', [$this, 'trickCanDelete']),
        ];
    }



    public function trickCanRead(Trick $item)
    {
        return $this->trickVoter->canRead($item, $this->user);
    }

    public function trickCanUpdate(Trick $item)
    {
        return $this->trickVoter->canUpdate($item, $this->user);
    }

    public function trickCanDelete(Trick $item)
    {
        return $this->trickVoter->canDelete($item, $this->user);
    }
}
