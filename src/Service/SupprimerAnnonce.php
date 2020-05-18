<?php

namespace App\Service;

use App\Entity\Anonce;
use App\Entity\Candidature;
use App\Entity\Competance;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class SupprimerAnnonce extends AbstractController{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
      $this->em = $em;
    }
  

    public function supp(Anonce $anonce){

        $listCandidatures = $this->em->getRepository(Candidature::class)->findBy(array('anonce' => $anonce));
        foreach($listCandidatures  as $candidature){
            $this->em->remove($candidature);
        }
  
        $competances = $this->em->getrepository(Competance::class)->findBy(array('anonce' => $anonce));
        foreach ($competances as $competances) {
        $this->em->remove($competances);
        }
  
        if($anonce->getImage()){
        unlink($this->getParameter('mes_images').'/'. $anonce->getImage());
        }
           
        $this->em->remove($anonce);
        $this->em->flush();
      }

}