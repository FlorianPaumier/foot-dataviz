<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 * @OA\Schema()
 */
class Player
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $pictureLink;

    /**
     * @var Collection|PlayerClub[]
     * @ORM\OneToMany(targetEntity="App\Entity\PlayerClub", mappedBy="player")
     */
    private $playerClubs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PlayerInformation", mappedBy="player")
     */
    private $information;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="players")
     */
    private $country;

    public function __construct()
    {
        $this->playerClubs = new ArrayCollection();
        $this->information = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPictureLink(): ?string
    {
        return $this->pictureLink;
    }

    public function setPictureLink(string $pictureLink): self
    {
        $this->pictureLink = $pictureLink;

        return $this;
    }


    /**
     * @return Collection|PlayerClub[]
     */
    public function getPlayerClubs(): Collection
    {
        return $this->playerClubs;
    }

    public function addPlayerClub(PlayerClub $playerClub): self
    {
        if (!$this->playerClubs->contains($playerClub)) {
            $this->playerClubs[] = $playerClub;
            $playerClub->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerClub(PlayerClub $playerClub): self
    {
        if ($this->playerClubs->contains($playerClub)) {
            $this->playerClubs->removeElement($playerClub);
            // set the owning side to null (unless already changed)
            if ($playerClub->getPlayer() === $this) {
                $playerClub->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlayerInformation[]
     */
    public function getInformation(): Collection
    {
        return $this->information;
    }

    public function addInformation(PlayerInformation $information): self
    {
        if (!$this->information->contains($information)) {
            $this->information[] = $information;
            $information->setPlayer($this);
        }

        return $this;
    }

    public function removeInformation(PlayerInformation $information): self
    {
        if ($this->information->contains($information)) {
            $this->information->removeElement($information);
            // set the owning side to null (unless already changed)
            if ($information->getPlayer() === $this) {
                $information->setPlayer(null);
            }
        }

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}
