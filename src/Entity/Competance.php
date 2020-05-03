<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="competance")
 * @ORM\Entity(repositoryClass="App\Repository\CompetanceRepository")
 */
class Competance
{
  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @ORM\Column(name="nom", type="string", length=255)
   */
  private $nom;

  public function getId()
  {
    return $this->id;
  }

  public function setNom($nom)
  {
    $this->nom = $nom;
  }

  public function getNom()
  {
    return $this->nom;
  }
}