<?php

namespace App\Entity;

use App\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PictureRepository;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 */
class Picture implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $path;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pictures", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $addedBy;

    /**
     * @ORM\OneToOne(targetEntity=Trick::class, mappedBy="mainPicture", cascade={"persist", "remove"})
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    private $mainToTrick;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

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

    public function getMainToTrick(): ?Trick
    {
        return $this->mainToTrick;
    }

    public function setMainToTrick(?Trick $mainToTrick): self
    {
        // unset the owning side of the relation if necessary
        if ($mainToTrick === null && $this->mainToTrick !== null) {
            $this->mainToTrick->setMainPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($mainToTrick !== null && $mainToTrick->getMainPicture() !== $this) {
            $mainToTrick->setMainPicture($this);
        }

        $this->mainToTrick = $mainToTrick;

        return $this;
    }
}
