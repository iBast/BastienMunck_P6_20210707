<?php

namespace App\Manager;

use App\Entity\Media;
use App\Entity\Trick;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class MediaManager
{
    protected $mediaRepository;
    protected $em;
    protected $security;

    public function __construct(MediaRepository $mediaRepository, EntityManagerInterface $em, Security $security)
    {
        $this->mediaRepository = $mediaRepository;
        $this->em = $em;
        $this->security = $security;
    }

    public function parseLink($link)
    {
        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=embed/)[^&\n]+| (?<=youtu.be/)[^&\n]+#", $link, $matches);
        return 'https://www.youtube.com/embed/' . $matches[0];
    }

    public function verifyLink($link)
    {
        if (strpos($link, 'youtube') == false) {
            return false;
        }
    }

    public function save(Media $video, Trick $trick, $link)
    {
        $video->setAddedBy($this->security->getUser())
            ->setTrick($trick)
            ->setType('Youtube')
            ->setlink($link);
        $this->em->persist($video);
        $this->em->flush();
    }
}
