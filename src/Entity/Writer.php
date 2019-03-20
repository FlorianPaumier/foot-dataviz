<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WriterRepository")
 */
class Writer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Embedded(class="App\Entity\People",columnPrefix = false)
     */
    private $people;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Movie", mappedBy="writers")
     */
    private $movies;

    public function __construct()
    {
        $this->movies = new ArrayCollection();
        $this->people = new People();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Movie[]
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies[] = $movie;
            $movie->addWriter($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->contains($movie)) {
            $this->movies->removeElement($movie);
            $movie->removeWriter($this);
        }

        return $this;
    }

    public function getPeople(): People
    {
        return $this->people;
    }

    public function setPeople(People $people): self
    {
        $this->people = $people;

        return $this;
    }
}
