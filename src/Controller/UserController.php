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
use App\Service\SupprimerAnnonce;


class UserController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em){
      $this->em = $em;
    }

    public function loginAction(AuthenticationUtils $autheutili)
    {
      if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        return $this->redirectToRoute('accueil');
      }
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
            return $this->redirectToRoute('modifier_profile');
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
    public function listeUsers($page){
      $user = new User();
      $user = new User();
      if ($page < 1) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);
      }
      $nbParPage = 3; 
      $users = $this->em->getRepository(User::class)->findAllUsers($page , $nbParPage);

      $nbPages = ceil(count($users) / $nbParPage);
        
      if ($page > $nbPages && $nbPages>0) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);;
      }

      return $this->render('Utilisateur/listeUsers.html.twig' , [
        'users' => $users,
        'nbPages' => $nbPages,
        'page' => $page,
      ]);

    }
    /**
    * @IsGranted("ROLE_ADMIN")    
    */
    public function OneUser(Request $request){
      $user = new User();

      $terme = $request->get('terme');
      $user = $this->em->getRepository(User::class)->findOneByUsername($terme);

      return $this->render('Utilisateur/usersearch.html.twig' , [
        'user' => $user,
      ]);
    }

    /**
    * @IsGranted("ROLE_ADMIN")    
    */
    public function suppUser($id , SupprimerAnnonce $supp){
      $user = new User();

      $user = $this->em->getRepository(User::class)->find($id);
      if (null === $user) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cet utilisateur n'existe pas!!" ]);
      }
        $anonces = $this->em->getRepository(Anonce::class)->findBy(array('user' => $user));

        foreach ($anonces as $anonce) {
          $supp->supp($anonce);
        }
        $this->em->remove($user);
        $this->em->flush();

        $this->addFlash('info', "L'utilisateur a bien été supprimée.");
  
        return $this->redirectToRoute('liste_users' , array('page' => 1));
      }

}
