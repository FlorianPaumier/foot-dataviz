<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 */
class Club
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="clubs")
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Match", mappedBy="homeTeam")
     */
    private $matches;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Match", mappedBy="awayTeam")
     */
    private $awayMatches;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->awayMatches = new ArrayCollection();
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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection|Match[]
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    public function addMatch(Match $match): self
    {
        if (!$this->matches->contains($match)) {
            $this->matches[] = $match;
            $match->setHomeTeam($this);
        }

        return $this;
    }

    public function removeMatch(Match $match): self
    {
        if ($this->matches->contains($match)) {
            $this->matches->removeElement($match);
            // set the owning side to null (unless already changed)
            if ($match->getHomeTeam() === $this) {
                $match->setHomeTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Match[]
     */
    public function getAwayMatches(): Collection
    {
        return $this->awayMatches;
    }

    public function addAwayMatch(Match $awayMatch): self
    {
        if (!$this->awayMatches->contains($awayMatch)) {
            $this->awayMatches[] = $awayMatch;
            $awayMatch->setAwayTeam($this);
        }

        return $this;
    }

    public function removeAwayMatch(Match $awayMatch): self
    {
        if ($this->awayMatches->contains($awayMatch)) {
            $this->awayMatches->removeElement($awayMatch);
            // set the owning side to null (unless already changed)
            if ($awayMatch->getAwayTeam() === $this) {
                $awayMatch->setAwayTeam(null);
            }
        }

        return $this;
    }
}
