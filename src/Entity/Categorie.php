<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
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

  /**
   * @return int
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @param string $nom
   */
  public function setNom($nom)
  {
    $this->nom = $nom;
  }

  /**
   * @return string
   */
  public function getNom()
  {
    return $this->nom;
  }
}