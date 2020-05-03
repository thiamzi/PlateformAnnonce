<?php

namespace App\Service;

use App\Entity\Candidature;
use App\Entity\Anonce;

class EnvoyerEmail
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;

  public function __construct(\Swift_Mailer $mailer)
  {
    $this->mailer = $mailer;
  }

  public function sendNewNotification(Candidature $candidature , Anonce $anonce)
  {
    $message = new \Swift_Message(
      'Nouvelle candidature',
      'Vous avez reçu une nouvelle candidature.'
    );

    $message
      ->setTo($anonce->getUser()->getEmail()) 
      ->setFrom('thiamzirabm@gmail.com')
      >setBody(
        $this->renderView(
          'layout/email.txt.twig',
          ['name' => $candidature]
        )
      )
    ;

    $this->mailer->send($message);
  }
}
