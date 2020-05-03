<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
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
   * @Assert\Length(min=2 , minMessage="Le nom de L'auteur doit faire au moins 2 caractères.")
   */
  private $auteur;

  /**
   * @ORM\Column(name="email" , type="string" , length=100)
   * @Assert\NotBlank()
   * @Assert\Email()
   */
  private $email;

  /**
   * @ORM\Column(name="contenu", type="text")
   * @Assert\NotBlank()
   */
  private $contenu;

  /**
   * @ORM\Column(name="date", type="datetime")
   */
  private $date;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Anonce" , inversedBy="candidatures")
   * @ORM\JoinColumn(nullable=false)
   */
  private $anonce;
  
  public function __construct()
  {
    $this->date = new \Datetime();
  }

  /**
   * @return int 
   */
  public function getId()
  {
    return $this->id;
  }

  public function setAuthor($auteur)
  {
    $this->auteur = $auteur;

    return $this;
  }

  /**
   * @return int 
   */
  public function getAuthor()
  {
    return $this->auteur;
  }

  public function setContent($contenu)
  {
    $this->content = $contenu;

    return $this;
  }

  /**
   * @return int 
   */
  public function getContent()
  {
    return $this->contenu;
  }

  /**
   * @param \Datetime $date
   */
  public function setDate(\Datetime $date)
  {
    $this->date = $date;

    return $this;
  }

  /**
   * @return \Datetime
   */
  public function getDate()
  {
    return $this->date;
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
   * @return Anonce
   */
  public function getAnonce(): ?Anonce
  {
      return $this->anonce;
  }

  /**
   * @param Anonce $anonce
   */
  public function setAnonce(?Anonce $anonce): self
  {
      $this->anonce = $anonce;

      return $this;
  }
  
  /**
  * @ORM\PrePersist
  */
  public function incrementer(){
    $this->getAnonce()->incrementerCandidature();
  }
  /**
   * ORM\PreRemove
   */
  public function decremeneter(){
    $this->getAnonce()->decrementerCandidature();
  }

  /**
   * @return string
   */
  public function getEmail(): ?string
  {
      return $this->email;
  }

  /**
   * @param string $email
   */
  public function setEmail(string $email): self
  {
      $this->email = $email;

      return $this;
  }
}