<?php

namespace App\Entity;

use App\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MediaRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=3, max=255, minMessage="The name has to have at least 3 characters", maxMessage="The name can't have more than 255 characters")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(message="The media should be an url")
     */
    private $link;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="media")
     * @ORM\JoinColumn(nullable=false)
     */
    private $addedBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getAddedBy(): ?User
    {
        return $this->addedBy;
    }

    public function setAddedBy(?User $addedBy): self
    {
        $this->addedBy = $addedBy;

        return $this;
    }
}
