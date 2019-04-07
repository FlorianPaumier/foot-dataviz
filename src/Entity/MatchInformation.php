<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchInformationRepository")
 */
class MatchInformation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $score;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MatchParameters")
     */
    private $parameter;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Match", inversedBy="informations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getParameter(): ?MatchParameters
    {
        return $this->parameter;
    }

    public function setParameter(?MatchParameters $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getGame(): ?Match
    {
        return $this->game;
    }

    public function setGame(?Match $game): self
    {
        $this->game = $game;

        return $this;
    }
}
