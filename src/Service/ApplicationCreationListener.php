<?php

namespace App\Service;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Service\EnvoyerEmail;
use App\Entity\Candidature;

class ApplicationCreationListener
{
  /**
   * @var EnvoyerEmail
   */
  private $envoyeremail;

  public function __construct(EnvoyerEmail $envoyeremail)
  {
    $this->envoyeremail = $envoyeremail;
  }

  public function postPersist(LifecycleEventArgs $args)
  {
    $entity = $args->getObject();

    // On ne veut envoyer un email que pour les entités Application
    if (!$entity instanceof Candidature) {
      return;
    }

    //$this->envoyeremail->sendNewNotification($entity , );
  }
}
