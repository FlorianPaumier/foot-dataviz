<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchRepository")
 * @OA\Schema()
 */
class Game
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $playingDate;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="matches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $homeTeam;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="awayMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $awayTeam;

    /**
     * @var Collection|MatchInformation[]
     * @ORM\OneToMany(targetEntity="App\Entity\MatchInformation", mappedBy="game", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $informations;

    /**
     * @var League
     * @ORM\ManyToOne(targetEntity="App\Entity\League", inversedBy="games")
     */
    private $league;

    public function __construct()
    {
        $this->informations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayingDate(): ?\DateTime
    {
        return $this->playingDate;
    }

    public function setPlayingDate(\DateTime $playingDate): self
    {
        $this->playingDate = $playingDate;

        return $this;
    }

    public function getHomeTeam(): ?Club
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(?Club $homeTeam): self
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?Club
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(?Club $awayTeam): self
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    /**
     * @return Collection|MatchInformation[]
     */
    public function getInformations(): Collection
    {
        return $this->informations;
    }

    public function addInformation(MatchInformation $information): self
    {
        if (!$this->informations->contains($information)) {
            $this->informations[] = $information;
            $information->setGame($this);
        }

        return $this;
    }

    public function removeInformation(MatchInformation $information): self
    {
        if ($this->informations->contains($information)) {
            $this->informations->removeElement($information);
            // set the owning side to null (unless already changed)
            if ($information->getGame() === $this) {
                $information->setGame(null);
            }
        }

        return $this;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

        return $this;
    }
}
