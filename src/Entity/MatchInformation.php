<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Symfony\Component\DependencyInjection\Tests\Compiler\G;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchInformationRepository")
 * @OA\Schema()
 */
class MatchInformation
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
     * @ORM\Column(type="string")
     */
    private $score;

    /**
     * @var MatchParameters
     * @ORM\ManyToOne(targetEntity="App\Entity\MatchParameters")
     */
    private $parameter;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="informations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): self
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
