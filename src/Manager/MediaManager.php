<?php

namespace App\Manager;

use App\Entity\EntityInterface;
use App\Entity\Media;
use App\Entity\Trick;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class MediaManager extends AbstractManager
{
    protected $mediaRepository;
    protected $manager;
    protected $security;

    public function __construct(MediaRepository $mediaRepository, EntityManagerInterface $manager, Security $security)
    {
        $this->mediaRepository = $mediaRepository;
        $this->security = $security;
        parent::__construct($manager);
    }

    public function initialise(EntityInterface $entity): void
    {
        /** @var Media */
        $video = $entity;

        $video->setAddedBy($this->security->getUser())
            ->setType('Youtube');
    }

    public function parseLink($link)
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=embed/)[^&\n]+| (?<=youtu.be/)[^&\n]+#", $link, $matches);
        return 'https://www.youtube.com/embed/' . $matches[0];
    }

    public function verifyLink($link)
    {
        if (strpos($link, 'youtube') > 1) {
            return true;
        }

        return false;
    }
}
