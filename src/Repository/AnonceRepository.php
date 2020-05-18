<?php

namespace App\Repository;

use App\Entity\Anonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method Anonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Anonce[]    findAll()
 * @method Anonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anonce::class);
    }

    public function findMyAnonce($page , $nbParPage)
    {
      $query = $this->createQueryBuilder('a')
        ->andWhere('a.publie = :publie')
        ->setParameter('publie', true)
        // Jointure sur l'attribut categories
        ->leftJoin('a.categories', 'c')
        ->addSelect('c')
        ->orderBy('a.date', 'DESC')
        ->getQuery()
      ;

      $query
      // On définit l'annonce à partir de laquelle commencer la liste
      ->setFirstResult(($page-1) * $nbParPage)
      // Ainsi que le nombre d'annonce à afficher sur une page
      ->setMaxResults($nbParPage);

    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
    // (n'oubliez pas le use correspondant en début de fichier)
    return new Paginator($query, true);
    }

    public function UserAnonces($page , $nbParPage , $user)
    {
      $query = $this->createQueryBuilder('a')
        ->andWhere('a.user = :user')
        ->setParameter('user', $user)
        // Jointure sur l'attribut categories
        ->orderBy('a.date', 'DESC')
        ->getQuery()
      ;

      $query
      // On définit l'annonce à partir de laquelle commencer la liste
      ->setFirstResult(($page-1) * $nbParPage)
      // Ainsi que le nombre d'annonce à afficher sur une page
      ->setMaxResults($nbParPage);

    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
    // (n'oubliez pas le use correspondant en début de fichier)
    return new Paginator($query, true);
    }


    public function AnonceApurger(\Datetime $date){

      $qb = $this->createQueryBuilder('a');

      $qb
        ->where('a.updatedAt <= :date')                      // Date de modification antérieure à :date
        ->orWhere('a.updatedAt IS NULL AND a.date <= :date') // Si la date de modification est vide, on vérifie la date de création
        ->andWhere('a.candidature IS EMPTY')                // On vérifie que l'annonce ne contient aucune candidature
        ->setParameter('date', $date)
      ;

      return $qb
      ->getQuery()
      ->getResult()
      ;
    }

    public function CategorieAnonces($page , $nbParPage , $categories)
    {
        $query = $this->createQueryBuilder('a')
          ->andWhere('a.categories = :categories')
          ->setParameter('categories', $categories)
          // Jointure sur l'attribut categories
          ->orderBy('a.date', 'DESC')
          ->getQuery()
        ;
  
        $query
        // On définit l'annonce à partir de laquelle commencer la liste
        ->setFirstResult(($page-1) * $nbParPage)
        // Ainsi que le nombre d'annonce à afficher sur une page
        ->setMaxResults($nbParPage);
  
      // Enfin, on retourne l'objet Paginator correspondant à la requête construite
      // (n'oubliez pas le use correspondant en début de fichier)
      return new Paginator($query, true);
      }

      public function search($page , $nbParPage , $term){

        $query = $this
          ->createQueryBuilder('a')
          ->where('a.titre LIKE :titre')
          ->setParameter('titre', '%'.$term.'%')
          ->orderBy('a.titre', 'asc')
        ;
        $query
        ->setFirstResult(($page-1) * $nbParPage)
        ->setMaxResults($nbParPage);
  
      return new Paginator($query, true);
    }


    public function isFlood($id , $seconde){
      
    }












    // /**
    //  * @return Anonce[] Returns an array of Anonce objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Anonce
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /*public function whereCurrentYear(QueryBuilder $qb)
    {
      $qb
        ->andWhere('a.date BETWEEN :start AND :end')
        ->setParameter('start', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
        ->setParameter('end',   new \Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
      ;
    }*/
    
   

    /*public function getAnonceAvecCategories(array $categoryNames)
    {
      $qb = $this->createQueryBuilder('a');
  
      // On fait une jointure avec l'entité Category avec pour alias « c »
      $qb
        ->innerJoin('a.categories', 'c')
        ->addSelect('c')
      ;
  
      // Puis on filtre sur le nom des catégories à l'aide d'un IN
      $qb->where($qb->expr()->in('c.name', $categoryNames));
      // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine
  
      // Enfin, on retourne le résultat
      return $qb
        ->getQuery()
        ->getResult()
      ;
    }*/

    /*public function getCandidatureAbonce($limit)
    {
      $qb = $this->createQueryBuilder('a');
  
      // On fait une jointure avec l'entité Advert avec pour alias « adv »
      $qb
        ->innerJoin('a.anonce', 'anp')
        ->addSelect('ano')
      ;
  
      // Puis on ne retourne que $limit résultats
      $qb->setMaxResults($limit);
  
      // Enfin, on retourne le résultat
      return $qb
        ->getQuery()
        ->getResult()
        ;
    }*/
}