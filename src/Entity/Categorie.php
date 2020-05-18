<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Anonce", mappedBy="categories")
     */
    private $anonce;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->anonce = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Anonce[]
     */
    public function getAnonce(): Collection
    {
        return $this->anonce;
    }

    public function addAnonce(Anonce $anonce): self
    {
        if (!$this->anonce->contains($anonce)) {
            $this->anonce[] = $anonce;
            $anonce->setCategories($this);
        }

        return $this;
    }

    public function removeAnonce(Anonce $anonce): self
    {
        if ($this->anonce->contains($anonce)) {
            $this->anonce->removeElement($anonce);
            // set the owning side to null (unless already changed)
            if ($anonce->getCategories() === $this) {
                $anonce->setCategories(null);
            }
        }

        return $this;
    }

   
}
