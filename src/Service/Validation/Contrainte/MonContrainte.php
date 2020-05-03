<?php
// src/OC/PlatformBundle/Validator/Antiflood.php

namespace App\Service\Validation\Contrainte;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MonContrainte extends Constraint
{
  public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";

  public function validatedBy()
  {
    return 'mon_validator'; // Ici, on fait appel à l'alias du service
  }
}