<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerAttributRepository")
 * @OA\Schema()
 * @JMS\ExclusionPolicy("all")
 */
class PlayerAttribut
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     * @JMS\Groups({"player_attribut"})
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     * @JMS\Expose()
     * @JMS\Groups({"player_attribut"})
     */
    private $score;

    /**
     * @var Attribut
     * @ORM\ManyToOne(targetEntity="App\Entity\Attribut")
     * @JMS\Expose()
     * @JMS\Groups({"player_attribut"})
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
