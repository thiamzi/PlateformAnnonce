<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="anonce_competence")
 * @ORM\Entity(repositoryClass="App\Repository\AnonceCompetanceRepository")
 */
class AnonceCompetance
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="niveau", type="string", length=255)
   */
  private $niveau;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Anonce")
   * @ORM\JoinColumn(nullable=false)
   */
  private $anonce;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Competance")
   * @ORM\JoinColumn(nullable=false)
   */
  private $competance;

  /**
   * @return int
   */
  public function getId(): ?int
  {
      return $this->id;
  }

  /**
   * @return string
   */
  public function getNiveau(): ?string
  {
      return $this->niveau;
  }

  /**
   * @param string $niveau
   */
  public function setNiveau(string $niveau): self
  {
      $this->niveau = $niveau;

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
   * @return Competance
   */
  public function getCompetance(): ?Competance
  {
      return $this->competance;
  }

  /**
   * @param Competance $competance
   */
  public function setCompetance(?Competance $competance): self
  {
      $this->competance = $competance;

      return $this;
  }

  
  // ... vous pouvez ajouter d'autres attributs bien sûr
}