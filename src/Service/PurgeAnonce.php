<?php

namespace App\Service;

use App\Entity\Anonce;
use App\Entity\AnonceCompetance;
use Doctrine\ORM\EntityManagerInterface;

class PurgeAnonce
{
  private $em;
  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  public function purge($jours){
    
    // date d'il y a $days jours
    $date = new \Datetime($jours.' days ago');

    // On récupère les annonces à supprimer
    $listeAnonces = $this->em->getrepository(Anonce::class)->Anoncepurge($date);

    // On parcourt les annonces pour les supprimer effectivement
    foreach ($listeAnonces as $anonce) {
      // On récupère les AdvertSkill liées à cette annonce
      $anoncesCompetances = $this->em->getrepository(AnonceCompetance::class)->findBy(array('anonce' => $anonce));

      // Pour les supprimer toutes avant de pouvoir supprimer l'annonce elle-même
      foreach ($anoncesCompetances as $anonceCompetance) {
        $this->em->remove($anonceCompetance);
      }

      // On peut maintenant supprimer l'annonce
      $this->em->remove($anonce);
    }

    // Et on n'oublie pas de faire un flush !
    $this->em->flush();
  }
}
