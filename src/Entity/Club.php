<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ClubRepository")
 * @OA\Schema()
 * @JMS\ExclusionPolicy("all")
 */
class Club
{
    /**
     * @var Integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     * @JMS\Groups({"club", "club_light","club_id"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     * @JMS\Expose()
     * @JMS\Groups({"club", "club_light"})
     */
    private $name;

    /**
     * @var Country
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="clubs")
     * @JMS\Expose()
     * @JMS\Groups({"club", "club_light"})
     */
    private $country;

    /**
     * @var Collection|Game[]
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="homeTeam")
     * @JMS\Expose()
     * @JMS\Groups({"club"})
     */
    private $matches;

    /**
     * @var Collection|Game[]
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="awayTeam")
     * @JMS\Expose()
     * @JMS\Groups({"club"})
     */
    private $awayMatches;

    /**
     * @var Collection|PlayerClub[]
     * @ORM\OneToMany(targetEntity="App\Entity\PlayerClub", mappedBy="club")
     * @JMS\Expose()
     * @JMS\Groups({"club"})
     */
    private $playerClubs;

    public function __construct()
    {
        $this->matches = new ArrayCollection();
        $this->awayMatches = new ArrayCollection();
        $this->playerClubs = new ArrayCollection();
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
     * @return Collection|Game[]
     */
    public function getMatches(): Collection
    {
        return $this->matches;
    }

    public function addMatch(Game $match): self
    {
        if (!$this->matches->contains($match)) {
            $this->matches[] = $match;
            $match->setHomeTeam($this);
        }

        return $this;
    }

    public function removeMatch(Game $match): self
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
     * @return Collection|Game[]
     */
    public function getAwayMatches(): Collection
    {
        return $this->awayMatches;
    }

    public function addAwayMatch(Game $awayMatch): self
    {
        if (!$this->awayMatches->contains($awayMatch)) {
            $this->awayMatches[] = $awayMatch;
            $awayMatch->setAwayTeam($this);
        }

        return $this;
    }

    public function removeAwayMatch(Game $awayMatch): self
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
            $playerClub->setClub($this);
        }

        return $this;
    }

    public function removePlayerClub(PlayerClub $playerClub): self
    {
        if ($this->playerClubs->contains($playerClub)) {
            $this->playerClubs->removeElement($playerClub);
            // set the owning side to null (unless already changed)
            if ($playerClub->getClub() === $this) {
                $playerClub->setClub(null);
            }
        }

        return $this;
    }
}
