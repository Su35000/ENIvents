<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //   ###########  il faut réduire le nombre de queries sur la page d'accueil  ###########

    public function findAllSorties()
    {
        $qb = $this->createQueryBuilder('s');
        //liaison avec la table particpant
        $qb->leftJoin('s.inscriptions', 'i');
        $qb->leftJoin('i.participant', 'participant');
        $qb->join('s.etat', 'etat');
        $qb->join('s.lieu', 'lieu');
        $qb->join('lieu.ville', 'ville');
        $qb->addSelect('i');
        $qb->addSelect('lieu');
        $qb->addSelect('ville');
        $qb->addSelect('etat');
        $qb->addSelect('participant');
        $qb->addOrderBy('s.etat' ,'ASC');
        $qb->addOrderBy('s.dateHeureDebut' ,'DESC');

        $query = $qb->getQuery()->getResult();

        return $query;
    }

    public function findAllSortiesParticipeesPar(Participant $participant)
    {

        //je récupère toutes mes sorties
        $qb = $this->createQueryBuilder('s');
        //liaison avec la table inscription
        $qb->leftJoin('s.inscriptions', 'i');
        $qb->leftJoin('i.participant', 'p');
        $qb->Where('i.participant IN (:participant)')
            ->setParameter('participant', $participant)
            ->orderBy('s.dateHeureDebut' ,'DESC');

        $query = $qb->getQuery()->getResult();
        return $query;
    }

    public function findAllSortiesOrganiseesPar(Participant $organisateur)
    {

        //je récupère toutes mes sorties
        $qb = $this->createQueryBuilder('s');
        //liaison avec la table inscription
        $qb->leftJoin('s.inscriptions', 'i');
        $qb->leftJoin('i.participant', 'p');
        $qb->Where('s.organisateur IN (:participant)')
            ->setParameter('participant', $organisateur)
            ->orderBy('s.dateHeureDebut' ,'DESC');


        $query = $qb->getQuery()->getResult();
        return $query;
    }

    public function sortiePlusDunMois(): array
    {
        //SELECT * FROM sortie WHERE date_cloture <= DATE_ADD(CURRENT_DATE, INTERVAL 1 MONTH) AND id_etat = 57
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT * FROM App\Entity\Sortie
                WHERE date_cloture <= DATE_ADD(CURRENT_DATE, INTERVAL 1 MONTH) 
                AND id_etat = 57'
        )->getOneOrNullResult();

        return $query->getResult();
    }

    public function annulationSortie(int $id): ?Sortie
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'UPDATE App\Entity\Sortie
            SET etat_id = 40
            WHERE id = :query'
        )->setParameter('query', $id)
         ->getOneOrNullResult();

        return $query->getResult();
    }

    public function motifAnnulationSortie(int $id, string $text): ?Sortie
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'UPDATE App\Entity\Sortie
            SET motif = ":text"
            WHERE id = :query'
        )->setParameter('query', $id)
         ->setParameter('text', $text)
         ->getOneOrNullResult();

        return $query->getResult();
    }

    public function findByFilters($contient,$dateDebut,$dateFin,$filtreOrga,$filtreInscrit,$filtrePasInscrit,$filtreSortiesPasse,$user)
    {
        $query = $this->createQueryBuilder('s')
            ->leftJoin('s.inscriptions', 'i')
            ->leftJoin('i.participant', 'p')
        ;


        if(isset($contient)){

            $query = $query
                ->andWhere('s.nom LIKE :contient')
                ->setParameter('contient', "%{$contient}%");}

          //  $dql = $dql . " AND (s.nom LIKE '%" . $contient . "%' OR s.description LIKE '%" . $contient . "%')";

        if(isset($dateDebut)){
            $query = $query
                ->andWhere('s.dateHeureDebut > :date')
                ->setParameter('date', $dateDebut);}

        if(isset($dateFin)){
            $query = $query
                ->andWhere('s.dateHeureDebut < :date')
                ->setParameter('date', $dateFin);}

        if($filtreOrga === true){
            $query = $query
                ->andWhere('s.organisateur = :user')
                ->setParameter('user', $user);}

        if($filtreInscrit === true){
            $query = $query
                ->andWhere('i.participant IN (:participant)')
                ->setParameter('participant', $user);}



//        if($filtrePasInscrit === true){
//            $query = $query
//                ->andWhere('(:participant) i.participant')
////                ->andWhere(':user NOT MEMBER OF s.inscriptions.participant')
////                ->setParameter('user', $user);
//                ->setParameter('participant', $user);
//        }
//        dump($query->getQuery());


        if($filtreSortiesPasse === true){
            $query = $query
                ->andWhere('s.etat = :etat')
                ->setParameter('etat', 7);}

        $query = $query->getQuery()->getResult();

        return $query;
    }



//    public function findByFilters($contient)
//    {



//        $entityManager = $this->getEntityManager();
//
//        $dql = "SELECT s FROM App\Entity\Sortie s WHERE 1=1 ";
//
//        if(isset($contient))
//            $dql = $dql . " AND (s.nom LIKE '%" . $contient . "%' OR s.description LIKE '%" . $contient . "%')";
//
////        if(isset($dateDebut) && isset($dateFin)){
////
////            $sortieDate = $this->findByDateFilters($dateDebut,$dateFin);
////            dd($sortieDate);
////        }
//
//        $query = $entityManager->createQuery($dql);
//
//        return $query->getResult();
//    }

    public function findByDateFilters($dateDebut, $dateFin)
    {
        $entityManager = $this->getEntityManager();

        $dql = "SELECT s FROM App\Entity\Sortie s WHERE 1=1 ";

        if ($dateDebut != "")
            $dql = $dql . "(BETWEEN" . $dateDebut . "AND" . $dateFin . " );" ;


        $query = $entityManager->createQuery($dql);

        return $query->getResult();
    }

    public function filterSortiesByCriteria(Participant $organisateur, Participant $participant)
    {





        //je récupère toutes mes sorties
     /*   $qb = $this->createQueryBuilder('s');
        //liaison avec la table inscription
        $qb->leftJoin('s.inscriptions', 'i');
        $qb->leftJoin('i.participant', 'p');

        $qb->Where(expr()->('s.organisateur IN (:participant)')
            ->setParameter('participant', $organisateur)
            ->orderBy('s.dateHeureDebut' ,'DESC'));


        $qb->expr()->$qb->Where('i.participant IN (:participant)')
            ->setParameter('participant', $participant)
            ->orderBy('s.dateHeureDebut' ,'DESC');


        $query = $qb->getQuery()->getResult();
        return $query;*/




    }


//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


}
