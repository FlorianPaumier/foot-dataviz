<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerClubRepository")
 * @OA\Schema()
 */
class PlayerClub
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Club
     * @ORM\ManyToOne(targetEntity="App\Entity\Club", inversedBy="playerClubs")
     */
    private $club;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="playerClubs")
     */
    private $player;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $staredDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $endedDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getStaredDate(): ?\DateTime
    {
        return $this->staredDate;
    }

    public function setStaredDate(\DateTime $staredDate): self
    {
        $this->staredDate = $staredDate;

        return $this;
    }

    public function getEndedDate(): ?\DateTime
    {
        return $this->endedDate;
    }

    public function setEndedDate(\DateTime $endedDate): self
    {
        $this->endedDate = $endedDate;

        return $this;
    }
}
