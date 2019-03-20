<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MovieRepository")
 */
class Movie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $budget;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $imdbID;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $popularity;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $revenue;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $runtime;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Language", inversedBy="movies")
     */
    private $spokenLanguages;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Country", inversedBy="movies")
     */
    private $productionCountries;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", inversedBy="movies")
     */
    private $productionCompanies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genre", inversedBy="movies")
     */
    private $genres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Actor", inversedBy="movies")
     */
    private $actors;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Director", inversedBy="movies")
     */
    private $directors;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Writer", inversedBy="movies")
     */
    private $writers;

    public function __construct()
    {
        $this->spokenLanguages = new ArrayCollection();
        $this->productionCountries = new ArrayCollection();
        $this->productionCompanies = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->actors = new ArrayCollection();
        $this->directors = new ArrayCollection();
        $this->writers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(?int $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getImdbID(): ?string
    {
        return $this->imdbID;
    }

    public function setImdbID(string $imdbID): self
    {
        $this->imdbID = $imdbID;

        return $this;
    }

    public function getPopularity(): ?float
    {
        return $this->popularity;
    }

    public function setPopularity(?float $popularity): self
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getRevenue(): ?int
    {
        return $this->revenue;
    }

    public function setRevenue(int $revenue): self
    {
        $this->revenue = $revenue;

        return $this;
    }

    public function getRuntime(): ?int
    {
        return $this->runtime;
    }

    public function setRuntime(?int $runtime): self
    {
        $this->runtime = $runtime;

        return $this;
    }

    /**
     * @return Collection|Language[]
     */
    public function getSpokenLanguages(): Collection
    {
        return $this->spokenLanguages;
    }

    public function addSpokenLanguage(Language $spokenLanguage): self
    {
        if (!$this->spokenLanguages->contains($spokenLanguage)) {
            $this->spokenLanguages[] = $spokenLanguage;
        }

        return $this;
    }

    public function removeSpokenLanguage(Language $spokenLanguage): self
    {
        if ($this->spokenLanguages->contains($spokenLanguage)) {
            $this->spokenLanguages->removeElement($spokenLanguage);
        }

        return $this;
    }

    /**
     * @return Collection|Country[]
     */
    public function getProductionCountries(): Collection
    {
        return $this->productionCountries;
    }

    public function addProductionCountry(Country $productionCountry): self
    {
        if (!$this->productionCountries->contains($productionCountry)) {
            $this->productionCountries[] = $productionCountry;
        }

        return $this;
    }

    public function removeProductionCountry(Country $productionCountry): self
    {
        if ($this->productionCountries->contains($productionCountry)) {
            $this->productionCountries->removeElement($productionCountry);
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getProductionCompanies(): Collection
    {
        return $this->productionCompanies;
    }

    public function addProductionCompany(Company $productionCompany): self
    {
        if (!$this->productionCompanies->contains($productionCompany)) {
            $this->productionCompanies[] = $productionCompany;
        }

        return $this;
    }

    public function removeProductionCompany(Company $productionCompany): self
    {
        if ($this->productionCompanies->contains($productionCompany)) {
            $this->productionCompanies->removeElement($productionCompany);
        }

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActors(): Collection
    {
        return $this->actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actors->contains($actor)) {
            $this->actors[] = $actor;
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        if ($this->actors->contains($actor)) {
            $this->actors->removeElement($actor);
        }

        return $this;
    }

    /**
     * @return Collection|Director[]
     */
    public function getDirectors(): Collection
    {
        return $this->directors;
    }

    public function addDirector(Director $director): self
    {
        if (!$this->directors->contains($director)) {
            $this->directors[] = $director;
        }

        return $this;
    }

    public function removeDirector(Director $director): self
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
        }

        return $this;
    }

    /**
     * @return Collection|Writer[]
     */
    public function getWriters(): Collection
    {
        return $this->writers;
    }

    public function addWriter(Writer $writer): self
    {
        if (!$this->writers->contains($writer)) {
            $this->writers[] = $writer;
        }

        return $this;
    }

    public function removeWriter(Writer $writer): self
    {
        if ($this->writers->contains($writer)) {
            $this->writers->removeElement($writer);
        }

        return $this;
    }
}
