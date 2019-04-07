<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchRepository")
 */
class Match
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $playingDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="matches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $homeTeam;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="awayMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $awayTeam;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MatchInformation", mappedBy="game", orphanRemoval=true)
     */
    private $informations;

    public function __construct()
    {
        $this->informations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayingDate(): ?\DateTimeInterface
    {
        return $this->playingDate;
    }

    public function setPlayingDate(\DateTimeInterface $playingDate): self
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
}
