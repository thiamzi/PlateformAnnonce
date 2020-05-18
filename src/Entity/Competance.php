<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Competance
 *
 * @ORM\Table(name="competance")
 * @ORM\Entity
 */
class Competance
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
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
    */
    private $niveau;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Anonce", inversedBy="competance" , cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * 
    */
    private $anonce;

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

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): self
    {
        $this->niveau = $niveau;

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
