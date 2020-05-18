<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Anonce;
use App\Entity\Candidature;
use App\Entity\Categorie;
use App\Entity\Competance;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AnonceFormType;
use App\Form\CandidatureType;
use App\Service\PurgeAnonce;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Service\ImgUploader;
use Symfony\Component\HttpFoundation\File\File;
use App\Service\EnvoyerEmail;
use App\Service\SupprimerAnnonce;
use App\Form\SearchType;

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

    public function ListeAnoncesCategorie($page , Categorie $categorie){
  
        if ($page < 1) {
          return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);
        }
  
        $nbParPage = 3; 
  
        $anonces = $this->em->getRepository(Anonce::class)->CategorieAnonces($page , $nbParPage , $categorie->getId());

        $nbPages = ceil(count($anonces) / $nbParPage);
        
        if ($page > $nbPages && $nbPages>0) {
          return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);;
        }
  
        return $this->render('anonce/anonceCategorie.html.twig' , [
          'anonces' => $anonces,
          'nbPages'     => $nbPages,
          'page'        => $page,
          'categorie'   => $categorie,
        ]);
      }

    public function vue(Anonce $anonce){
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
      $listCompetances = $this->em->getRepository(Competance::class)->findBy(array('anonce' => $anonce));

      return $this->render('anonce/detail.html.twig' , [
        'anonce' => $anonce, 
        'categories' => $categories,
        'Candidatures' => $listCandidatures,
        'Competances' => $listCompetances,
        'verif' => $verif 
        ]);
    }


    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function ajout(Request $request , ImgUploader $fileUploader){

      $anonce = new Anonce();
      $form = $this->createForm(AnonceFormType::class, $anonce);

      $anonce->setAuteur($this->getUser()->getUsername());
      $anonce->setUser($this->getUser());
      $form->handleRequest($request);
      if ($request->isMethod('POST')) {
        // On vérifie que les valeurs entrées sont correctes
  
          /** @var UploadedFile $imageFile */
          $imageFile = $form['image']->getData();
          if ($imageFile) {
            $nom = $fileUploader->upload($imageFile);
            $anonce->setImage($nom);
          }
          
          if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $anonce->setPublie(false);
          }
          $this->em->persist($anonce);
          $this->em->flush();

          $competances = $anonce->getCompetance();

          foreach ($competances as $competance){
          $competance->setAnonce($anonce);
          }
          $this->em->flush();

          $this->addFlash('info', 'Annonce bien enregistrée.');
          return $this->redirectToRoute('anonce_detail', array('id' => $anonce->getId()));
          
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

      $form = $this->createForm(AnonceFormType::class, $anonce);

      $form->handleRequest($request);
      if ($request->isMethod('POST')) {
        // On vérifie que les valeurs entrées sont correctes
  
          /** @var UploadedFile $imageFile */
          $imageFile = $form['image']->getData();
          if ($imageFile) {
            $nom = $fileUploader->upload($imageFile);
            $anonce->setImage($nom);
          }

          $this->em->flush();

          $competances = $anonce->getCompetance();

          foreach ($competances as $competance){
          $competance->setAnonce($anonce);
          }
          $this->em->flush();

          $this->addFlash('info', 'Annonce bien mise en jour.');
          return $this->redirectToRoute('anonce_detail', array('id' => $anonce->getId()));
      }
        return $this->render('anonce/modifier.html.twig' , array(
          'form' => $form->createView(), 
          'anonce' => $anonce, 
        ));
    }


    /**
    * @IsGranted("ROLE_AUTEUR")    
    */
    public function supprimer(Request $request , $id ,  SupprimerAnnonce $supp){
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

        $supp->supp($anonce);
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
      if ($form->isSubmitted() && $form->isValid()) {
        $anonce->addCandidature($candidature);
        $this->em->persist($candidature);
        $this->em->flush();

        $email->notificationCandidature($candidature  , $anonce);

        $this->addFlash('info', 'Candidature bien enregistrée.');
      return $this->redirectToRoute('anonce_detail', array('id' => $anonce->getId()));
      }
    }
      return $this->render('anonce/candidature.html.twig' , array(
        'form' => $form->createView(),
      ));
  }
  /**
  * @IsGranted("ROLE_ADMIN")    
  */
  public function purgeAction($jours = 2, Request $request , PurgeAnonce $purger)
  {
    // On purge les annonces
    $i = $purger->purge($jours);

    // On ajoute un message flash arbitraire
    $this->addFlash('info', $i.' annonces vieilles plus que '.$jours.' jours ont été purgées.');

    // On redirige vers la page d'accueil
    return $this->redirectToRoute('accueil', array('page'  =>  1));
  }

  public function recherche($page , Request $request){

    $terme = $request->get('terme');

    $nbParPage = 3; 
    $anonces = $this->em->getRepository(Anonce::class)->search($page , $nbParPage ,$terme);
    $nbPages = ceil(count($anonces) / $nbParPage);
        
    if ($page > $nbPages && $nbPages>0) {
      return $this->render('anonce/notFound.html.twig' , ['notFound' => "Cette page n'existe pas!!" ]);;
    }

    return $this->render('anonce/recherche.html.twig', array(
      'anonces' => $anonces,
      'nbPages'     => $nbPages,
      'page'        => $page,
    ));
  }
}


