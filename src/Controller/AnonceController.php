<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\SpamService;
use App\Entity\Anonce;
use App\Entity\Candidature;
use App\Entity\Image;
use App\Entity\AnonceCompetance;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AnonceFormType;
use App\Form\ModifierAnonceType;
use App\Form\CandidatureType;
use App\Service\PurgeAnonce;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Service\security\controleurformLogin;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Service\ImgUploader;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\EnvoyerEmail;


class AnonceController extends AbstractController
{
  private $em;
  public function __construct(EntityManagerInterface $em ){
    $this->em = $em;
  }

    public function ListeAnonces($page){

      if ($page < 1) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);
      }


      // Ici je fixe le nombre d'annonces par page à 3
      // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
      $nbParPage = 3; 

      $listeAnonces = $this->em->getRepository(Anonce::class)->findMyAnonce($page, $nbParPage);

      // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
      $nbPages = ceil(count($listeAnonces) / $nbParPage);

      // Si la page n'existe pas, on retourne une 404
      if ($page > $nbPages && $nbPages>0) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);;
    }


      return $this->render('anonce/accueil.html.twig',
            ['listeAnonces' => $listeAnonces,
            'nbPages'     => $nbPages,
            'page'        => $page,
            ]
      );
    }

    public function vue($id){
      $anonce = $this->em->getRepository(Anonce::class)->find($id);
      if (null === $anonce) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cet Anonce n'existe pas!!" ]);
      }
      $verif = false;

      if($this->getUser() && $anonce->getUser()->getId() === $this->getUser()->getId() && $anonce->getUser() !==null){
        $verif = true;
      }
      $categories = $anonce->getCategories();

      // Récupération de la liste des candidatures de l'annonce
      $listCandidatures = $this->em->getRepository(Candidature::class)->findBy(array('anonce' => $anonce));

      // Récupération des AdvertSkill de l'annonce
      $listAnoncesCompetances = $this->em->getRepository(AnonceCompetance::class)->findBy(array('anonce' => $anonce));

      return $this->render('anonce/detail.html.twig' , [
        'anonce' => $anonce, 
        'categories' => $categories,
        'Candidatures' => $listCandidatures,
        'AnoncesCompet' => $listAnoncesCompetances,
        'verif' => $verif 
        ]);
    }


    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function ajout(Request $request , ImgUploader $fileUploader){

      $anonce = new Anonce();
      $form = $this->createForm(AnonceFormType::class, $anonce);

      $anonce->setAuteur($this->getUser()->getNom());
      $anonce->setUser($this->getUser());
      $form->handleRequest($request);
      
      if ($request->isMethod('POST')) {
        // On vérifie que les valeurs entrées sont correctes

        if ($form->isValid()) {
        /** @var UploadedFile $imageFile */
        $imageFile = $form['image']->getData();
        if ($imageFile) {
            $nom = $fileUploader->upload($imageFile);
            $anonce->setImage($nom);
        }
          $this->em->persist($anonce);
          $this->em->flush();
  
          $this->addFlash('info', 'Annonce bien enregistrée.');
          return $this->redirectToRoute('anonce_detail', array('id' => $anonce->getId()));
        }
      }
        return $this->render('anonce/ajout.html.twig' , array(
          'form' => $form->createView(),
        ));
    }


    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function modifier($id, Request $request , ImgUploader $fileUploader){

      $anonce = $this->em->getRepository(Anonce::class)->find($id);
      
      if (null === $anonce) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cet Anonce n'existe pas!!" ]);
      }

      if($anonce->getUser()->getId() !== $this->getUser()->getId()){
        return $this->render('Utilisateur/AccesRefuse.html.twig' , ['refuse' => "Acces refuse!!" ]);
      }

      $form = $this->createForm(ModifierAnonceType::class, $anonce);
      if ($request->isMethod('POST')) {

        $form->handleRequest($request);
        //verifier si formulaire est valide en faisant appel au service du validator
        if ($form->isValid()) {
        
        /** @var UploadedFile $imageFile */
        $imageFile = $form['image']->getData();
        if ($imageFile) {
          unlink($this->getParameter('mes_images').'/'. $anonce->getImage());
          $nom = $fileUploader->upload($imageFile);
          $anonce->setImage($nom);
        }

          $this->em->flush();

          $this->addFlash('info', 'Annonce bien mise en jour.');
          return $this->redirectToRoute('anonce_detail', array('id' => $anonce->getId()));
        }
      }

      else{
        return $this->render('anonce/modifier.html.twig' , array(
          'form' => $form->createView(), 
          'anonce' => $anonce, 
        ));
      }
    }


    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function supprimer(Request $request , $id){
      $anonce = $this->em->getRepository(Anonce::class)->find($id);
      if (null === $anonce) {
        return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cet Anonce n'existe pas!!" ]);
      }
      if($anonce->getUser()->getId() !== $this->getUser()->getId()){
        return $this->render('Utilisateur/AccesRefuse.html.twig' , ['refuse' => "Acces refuse!!" ]);
      }
      // On crée un formulaire vide, qui ne contiendra que le champ CSRF
      // Cela permet de protéger la suppression d'annonce contre cette faille
      $form = $this->get('form.factory')->create();
  
      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
        unlink($this->getParameter('mes_images').'/'. $anonce->getImage());
        $this->em->remove($anonce);
        $this->em->flush();
  
        $this->addFlash('info', "L'annonce a bien été supprimée.");
  
        return $this->redirectToRoute('accueil' , array('page' => 1));
      }
      
      return $this->render('anonce/supprimer.html.twig', array(
        'anonce' => $anonce,
        'form'   => $form->createView(),
      ));
    }

  public function candidature($id , Request $request , EnvoyerEmail $email){
    $candidature = new Candidature();
    $form = $this->createForm(CandidatureType::class, $candidature);
    
    $anonce = $this->em->getRepository(Anonce::class)->find($id);

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      // On vérifie que les valeurs entrées sont correctes
      // (Nous verrons la validation des objets en détail dans le prochain chapitre)
      if ($form->isValid()) {
        $anonce->addCandidature($candidature);
        $this->em->persist($candidature);
        $this->em->flush();

        $email->sendNewNotification($candidature  , $anonce);

        $this->addFlash('info', 'Candidature bien enregistrée.');
      return $this->redirectToRoute('anonce_detail', array('id' => $anonce->getId()));
      }
    }
    else{
      return $this->render('anonce/candidature.html.twig' , array(
        'form' => $form->createView(),
      ));
    }
  }
  /**
  * @IsGranted("ROLE_ADMIN")    
  */
  public function purgeAction($jours, Request $request , PurgeAnonce $puger)
  {
    // On purge les annonces
    $purger->purge($jours);

    // On ajoute un message flash arbitraire
    $this->addFlash('info', 'Les annonces plus vieilles que '.$jours.' jours ont été purgées.');

    // On redirige vers la page d'accueil
    return $this->redirectToRoute('accueil', array('page'  =>  1));
  }
}


