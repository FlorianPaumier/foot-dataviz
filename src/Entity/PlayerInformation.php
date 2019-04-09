<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerInformationRepository")
 * @OA\Schema()
 */
class PlayerInformation
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
    private $salary;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $effectiveDate;

    /**
     * @var Collection|Attribut[]
     * @ORM\OneToMany(targetEntity="App\Entity\PlayerAttribut", mappedBy="playerInformation")
     */
    private $attributs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="information")
     */
    private $player;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $position;

    /**
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $weight;

    public function __construct()
    {
        $this->attributs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setSalary(int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getEffectiveDate(): ?\DateTimeInterface
    {
        return $this->effectiveDate;
    }

    public function setEffectiveDate(\DateTimeInterface $effectiveDate): self
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * @return Collection|PlayerAttribut[]
     */
    public function getAttributs(): Collection
    {
        return $this->attributs;
    }

    public function addAttribut(PlayerAttribut $attribut): self
    {
        if (!$this->attributs->contains($attribut)) {
            $this->attributs[] = $attribut;
            $attribut->setPlayerInformation($this);
        }

        return $this;
    }

    public function removeAttribut(PlayerAttribut $attribut): self
    {
        if ($this->attributs->contains($attribut)) {
            $this->attributs->removeElement($attribut);
            // set the owning side to null (unless already changed)
            if ($attribut->getPlayerInformation() === $this) {
                $attribut->setPlayerInformation(null);
            }
        }

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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
