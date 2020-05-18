<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Anonce
 *
 * @ORM\Table(name="Anonce", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_1C01D8A989D9B62", columns={"slug"})}, indexes={@ORM\Index(name="IDX_1C01D8AA76ED395", columns={"user_id"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\AnonceRepository")
 * @UniqueEntity(fields="titre" , message="Une annonce existe déjà avec ce titre.")
 */
class Anonce
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="titre", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=4 , minMessage="The title should be at least 4 characters")
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=2 , minMessage="The name should be at least 2 characters")
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=0, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=4 , minMessage="The contain should be at least 4 characters")
     */
    private $contenu;

    /**
     * @var bool
     *
     * @ORM\Column(name="publie", type="boolean", nullable=false)
     */
    private $publie;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var string
     * 
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(name="slug", type="string", length=255, nullable=false)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     * 
     */
    private $image;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Candidature", mappedBy="anonce" )
    */
    private $candidature;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_applications", type="integer", nullable=false)
     */
    private $nbApplications = 0;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Categorie", inversedBy="anonce")
     * 
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Competance", mappedBy="anonce" , cascade={"persist"})
    */
    private $competance;


    public function __construct()
    {
        $this->date = new \Datetime();
        $this->candidature = new ArrayCollection();
        $this->competance = new ArrayCollection();
    }

      /**
   * @ORM\PreUpdate
   */
  public function updateDate()
  {
    $this->setUpdatedAt(new \Datetime());
  }

  public function incrementerCandidature()
  {
    $this->nbApplications++;
  }

  public function decrementerCandidature()
  {
    $this->nbApplications--;
  }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getPublie(): ?bool
    {
        return $this->publie;
    }

    public function setPublie(bool $publie): self
    {
        $this->publie = $publie;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getNbApplications(): ?int
    {
        return $this->nbApplications;
    }

    public function setNbApplications(int $nbApplications): self
    {
        $this->nbApplications = $nbApplications;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Candidature[]
     */
    public function getCandidature(): Collection
    {
        return $this->candidature;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidature->contains($candidature)) {
            $this->candidature[] = $candidature;
            $candidature->setAnonce($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidature->contains($candidature)) {
            $this->candidature->removeElement($candidature);
            // set the owning side to null (unless already changed)
            if ($candidature->getAnonce() === $this) {
                $candidature->setAnonce(null);
            }
        }

        return $this;
    }

    public function getCategories(): ?Categorie
    {
        return $this->categories;
    }

    public function setCategories(?Categorie $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection|Competance[]
     */
    public function getCompetance(): Collection
    {
        return $this->competance;
    }

    public function addCompetance(Competance $competance): self
    {
        if (!$this->competance->contains($competance)) {
            $this->competance[] = $competance;
            $competance->setAnonce($this);
        }

        return $this;
    }

    public function removeCompetance(Competance $competance): self
    {
        if ($this->competance->contains($competance)) {
            $this->competance->removeElement($competance);
            // set the owning side to null (unless already changed)
            if ($competance->getAnonce() === $this) {
                $competance->setAnonce(null);
            }
        }

        return $this;
    }

}
