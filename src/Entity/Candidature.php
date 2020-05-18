<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Anonce;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="candidature")
 * @ORM\Entity(repositoryClass="App\Repository\CandidatureRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Candidature
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="auteur", type="string", length=255)
   * @Assert\NotBlank()
   * @Assert\Length(min=2 , minMessage="The name should be at least 2 characters")
   */
  private $auteur;

/**
 * @var string
 *
 * @ORM\Column(name="email", type="string", length=180, nullable=false)
 * @Assert\NotBlank()
 * @Assert\Email()
 */
    private $email;

  /**
   * @ORM\Column(name="contenu", type="text")
   * @Assert\NotBlank()
   * @Assert\Length(min=2 , minMessage="The contain should be at least 2 characters")
   */
  private $contenu;

  /**
   * @ORM\Column(name="date", type="datetime")
   */
  private $date;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Anonce", inversedBy="candidature")
   * @ORM\JoinColumn(nullable=false)
   */
  private $anonce;

  public function __construct()
  {
    $this->date = new \Datetime();
  }

   /**
   * @ORM\PrePersist
   */
  public function increase()
  {
    $this->getAnonce()->incrementerCandidature();
  }

  /**
   * @ORM\PreRemove
   */
  public function decrease()
  {
    $this->getAnonce()->decrementerCandidature();
  }

  public function getId(): ?int
  {
      return $this->id;
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

  public function getDate(): ?\DateTimeInterface
  {
      return $this->date;
  }

  public function setDate(\DateTimeInterface $date): self
  {
      $this->date = $date;

      return $this;
  }

  public function getEmail(): ?string
  {
      return $this->email;
  }

  public function setEmail(string $email): self
  {
      $this->email = $email;

      return $this;
  }

  public function getAnonce(): ?Anonce
  {
      return $this->anonce;
  }

  public function setAnonce(?Anonce $anonce): self
  {
      $this->anonce = $anonce;

      return $this;
  }

 
}