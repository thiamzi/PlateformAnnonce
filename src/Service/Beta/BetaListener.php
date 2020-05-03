<?php

namespace App\Service\Beta;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class BetaListener
{
  // Notre processeur
  protected $betaHTML;

  // La date de fin de la version bêta :
  // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
  // - Après cette date, on n'affichera plus le « bêta »
  protected $endDate;

  public function __construct(BetaHTMLAdder $betaHTML, $endDate)
  {
    $this->betaHTML = $betaHTML;
    $this->endDate  = new \Datetime($endDate);
  }

  public function processBeta(ResponseEvent $event)
  {
    $remainingDays = $this->endDate->diff(new \Datetime())->days;

    if ($remainingDays <= 0) {
      // Si la date est dépassée, on ne fait rien
      return;
    }
    // On teste si la requête est bien la requête principale (et non une sous-requête)
    if (!$event->isMasterRequest()) {
        return;
      }
  
      // On récupère la réponse que le gestionnaire a insérée dans l'évènement
      $response = $event->getResponse();
  
      // On utilise notre BetaHRML
      $response = $this->betaHTML->addBeta($event->getResponse(), $remainingDays);
  
      // Puis on insère la réponse modifiée dans l'évènement
      $event->setResponse($response);
    }
}