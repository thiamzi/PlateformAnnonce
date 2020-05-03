<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Anonce
 *
 * @ORM\Table(name="Anonce")
 * @ORM\Entity(repositoryClass="App\Repository\AnonceRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @Assert\Length(min=10 , minMessage="Le titre doit faire au moins 10 caractères.")
     */
    private $titre;

    /**
     * @var string
     *
     * @ORM\Column(name="auteur", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=2 , minMessage="Le nom de L'auteur doit faire au moins 2 caractères.")
     */
    private $auteur;

    /**
     * @var string
     *
     * @ORM\Column(name="contenu", type="text", length=0, nullable=false)
     * @Assert\NotBlank()
     */
    private $contenu;

    /**
     * @var bool
     *
     * @ORM\Column(name="publie", type="boolean", nullable=false)
     */
    private $publie;

    /**
      * @ORM\Column(name="updated_at", type="datetime", nullable=true)
    */
    private $updatedAt;

    /** 
     * @Gedmo\Slug(fields={"titre"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
    */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=false)
     */
    private $image;

    /**
    * @ORM\ManyToMany(targetEntity="App\Entity\Categorie", cascade={"persist"})
    *
    * @ORM\JoinTable(name="appartient")
    */
     private $categories;

    /**
    * @ORM\OneToMany(targetEntity="App\Entity\Candidature", mappedBy="anonce")
    */
    private $candidatures;

    /**
     * @ORM\Column(name="nb_applications", type="integer")
     */
     private $nbCandidatures = 0;

    /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    */
    private $user;


    public function __construct(){
        $this->date = (new \Datetime());
        $this->publie = true;
        $this->categories = new ArrayCollection();
        $this->candidatures = new ArrayCollection();
    }

    /**
     * @return int
    */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
    * @return \DateTime
    */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
    * @param \DateTime $date
    */
    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
    * @return string
    */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
    * @param string $titre 
    */
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
    * @return string
    */
    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    /**
    * @param string $auteur 
    */
    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
    * @return string
    */
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    /**
    * @param string $contenu 
    */
    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
    * @return bool
    */
    public function getPublie(): ?bool
    {
        return $this->publie;
    }

    /**
    * @param bool $publie 
    */
    public function setPublie(bool $publie): self
    {
        $this->publie = $publie;

        return $this;
    }

    public function setImage(string $image)
    {
      $this->image = $image;
    }
  
    public function getImage() : ?string
    {
      return $this->image;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories()
    {
      return $this->categories;
    }

    /**
    * @param Categorie $category
    */
    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    /**
    * @param Categorie $category
    */
    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    /**
     * @return Collection|Candidature[]
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    /**
     * @param Candidature $candidature
    */
    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
            $candidature->setAnonce($this);
        }

        return $this;
    }

    /**
     * @param Candidature $candidature
    */
    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->contains($candidature)) {
            $this->candidatures->removeElement($candidature);
            // set the owning side to null (unless already changed)
            if ($candidature->getAnonce() === $this) {
                $candidature->setAnonce(null);
            }
        }

        return $this;
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
      $this->nbCandidatures++;
    }
  
    public function decrementerCandidature()
    {
      $this->nbCandidatures--;
    }

    /**
    * @return \DateTime
    */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
    * @param \DateTime $updatedAt
    */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
    * @return integer
    */
    public function getNbCandidatures(): ?int
    {
        return $this->nbCandidatures;
    }

    /**
    * @param integer $nbCandidatures
    */
    public function setNbCandidatures(int $nbCandidatures): self
    {
        $this->nbCandidatures = $nbCandidatures;

        return $this;
    }

    /**
    * @return string
    */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
    * @param string $slug
    */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
    * @Assert\Callback
    */
    public function isContentValid(ExecutionContextInterface $context)
    {
        $forbiddenWords = array('démotivation', 'abandon');

        // On vérifie que le contenu ne contient pas l'un des mots
        if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getContenu())) {
        // La règle est violée, on définit l'erreur
        $context
        ->buildViolation('Contenu invalide car il contient un mot interdit.') // message
        ->atPath('contenu')                                                   // attribut de l'objet qui est violé
        ->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
        ;
         }
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
}
