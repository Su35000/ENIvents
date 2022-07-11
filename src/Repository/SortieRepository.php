<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Inscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        $qb->leftJoin('s.organisateur', 'o');
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
