<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Anonce;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class UserController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em){
      $this->em = $em;
    }

    public function loginAction(AuthenticationUtils $autheutili)
    {

      // Le service authentication_utils permet de récupérer le nom d'utilisateur
      // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
      // (mauvais mot de passe par exemple
   
      return $this->render('Utilisateur\login.html.twig', array(
        'last_username' => $autheutili->getLastUsername(),
        'error'         => $autheutili->getLastAuthenticationError(),
      ));
    }
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->em->persist($user);
            $this->em->flush();

            // do anything else you need here, like send an email
            $this->addFlash('info', 'Annonce bien enregistrée.');
            return $this->redirectToRoute('loginn');
        }

        return $this->render('Utilisateur/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function Monprofile(){

      return $this->render('Utilisateur/Monprofile.html.twig');
    }
    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function ModiferProfile(Request $request, UserPasswordEncoderInterface $passwordEncoder){

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
  
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
  
            $this->em->persist($user);
            $this->em->flush();
  
            // do anything else you need here, like send an email
            $this->addFlash('info', 'Profil mis a jour avec succes');
            return $this->redirectToRoute('mon_profile');
        }
  
        return $this->render('Utilisateur/modifierProfile.html.twig', [
          'form' => $form->createView(),
        ]);
    }

    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function mesAnonces($page){

      $user = $this->getUser();

      if ($page < 1) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);
      }

      $nbParPage = 3; 

      $anonces = $this->em->getRepository(Anonce::class)->UserAnonces($page , $nbParPage , $user);

      $nbPages = ceil(count($anonces) / $nbParPage);
      
      if ($page > $nbPages && $nbPages>0) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);;
      }

      return $this->render('Utilisateur/userAnonces.html.twig' , array(
        'anonces' => $anonces,
        'nbPages'     => $nbPages,
        'page'        => $page,
      ));
    }
}
