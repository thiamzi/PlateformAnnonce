<?php

namespace App\Service;

use App\Entity\Anonce;
use App\Entity\Competance;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\EnvoyerEmail;

class PurgeAnonce
{
  private $em;
  private $email;
  public function __construct(EntityManagerInterface $em , EnvoyerEmail $email)
  {
    $this->em = $em;
    $this->email = $email;
  }

  public function purge($jours){
    
    // date d'il y a $days jours
    $date = new \Datetime($jours.' days ago');

    // On récupère les annonces à supprimer
    $listeAnonces = $this->em->getrepository(Anonce::class)->AnonceApurger($date);

    // On parcourt les annonces pour les supprimer effectivement
    $i = 0;
    foreach ($listeAnonces as $anonce) {
      // On récupère les AdvertSkill liées à cette annonce
      $competances = $this->em->getrepository(Competance::class)->findBy(array('anonce' => $anonce));

      // Pour les supprimer toutes avant de pouvoir supprimer l'annonce elle-même
      foreach ($competances as $anonceCocompetancesmpetance) {
        $this->em->remove($anonceCompetacompetancesnce);
      }

      // On peut maintenant supprimer l'annonce
      $this->email->notificationPurge($anonce);
      $this->em->remove($anonce);

      $i++;
    }

    // Et on n'oublie pas de faire un flush !
    $this->em->flush();
    return $i;
  }
  
}
