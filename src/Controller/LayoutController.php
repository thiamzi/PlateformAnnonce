<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categorie;

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

    public function Categorie()
    {
      $listeCategories = $this->em->getRepository(Categorie::class)->findBy(
        array(),
        array('nom' => 'asc')                
      );
  
      return $this->render('layout/categorie.html.twig',
        ['listeCategories' => $listeCategories]
      );
    }
}
