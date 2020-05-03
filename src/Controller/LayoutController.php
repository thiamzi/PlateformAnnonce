<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Anonce;

class LayoutController extends AbstractController
{
    private $em; 

    public function __construct(EntityManagerInterface $em){
      $this->em = $em;
    }

    public function index()
    {
      return $this->redirectToRoute('accueil', array('page' => 1));
    }

    public function DernierAnonces($limit)
    {
      $listeDerniersAnonces = $this->em->getRepository(Anonce::class)->findBy(
        array(),                 // Pas de critère
        array('date' => 'desc'), // On trie par date décroissante
        $limit,                  // On sélectionne $limit annonces
        0                        // À partir du premier
      );
  
      return $this->render('layout/listeAnonceMenu.html.twig' ,
        ['listeAnonces' => $listeDerniersAnonces]
      );
    }
}
