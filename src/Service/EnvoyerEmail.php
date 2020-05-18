<?php

namespace App\Service;

use App\Entity\Candidature;
use App\Entity\Anonce;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class EnvoyerEmail extends AbstractController
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;

  public function __construct(\Swift_Mailer $mailer)
  {
    $this->mailer = $mailer;
  }

  public function notificationCandidat(Candidature $candidature , Anonce $anonce)
  {
    $message = new \Swift_Message(
      'Nouvelle candidature',
      'Vous avez reçu une nouvelle candidature.'
    );

    $message
      ->setTo($anonce->getUser()->getEmail()) 
      ->setFrom('thiamzirabm@gmail.com')
      ->setBody(
        $this->renderView(
          'email/emailCandidat.txt.twig',
          ['candidature' => $candidature]
        )
      )
    ;
    $this->mailer->send($message);
  }

  public function notificationPurge(Anonce $anonce)
  {
    $message = new \Swift_Message(
      'Nouvelle purge'
    );

    $message
      ->setTo($anonce->getUser()->getEmail()) 
      ->setFrom('thiamzirabm@gmail.com')
      ->setBody(
        $this->renderView(
          'email/emailPurge.txt.twig',
          ['anonce' => $anonce]
        )
      )
    ;
    $this->mailer->send($message);
  }
}
