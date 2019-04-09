<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerAttributRepository")
 * @OA\Schema()
 */
class PlayerAttribut
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @var Attribut
     * @ORM\ManyToOne(targetEntity="App\Entity\Attribut")
     */
    private $attributs;

    /**
     * @var PlayerInformation
     * @ORM\ManyToOne(targetEntity="App\Entity\PlayerInformation", inversedBy="attributs")
     */
    private $playerInformation;

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

    public function getAttributs(): ?Attribut
    {
        return $this->attributs;
    }

    public function setAttributs(?Attribut $attributs): self
    {
        $this->attributs = $attributs;

        return $this;
    }

    public function getPlayerInformation(): ?PlayerInformation
    {
        return $this->playerInformation;
    }

    public function setPlayerInformation(?PlayerInformation $playerInformation): self
    {
        $this->playerInformation = $playerInformation;

        return $this;
    }
}
