<?php

namespace App\Repository;

use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Participant>
 *
 * @method Participant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Participant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Participant[]    findAll()
 * @method Participant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParticipantRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function add(Participant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Participant $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Participant) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }
    public function loadUserByIdentifier(string $identifier): ?Participant
    {
        $entityManager = $this->getEntityManager();
        return $entityManager->createQuery(
            'SELECT p
            FROM App\Entity\Participant p
            WHERE p.username = :query
            OR p.email = :query'
        )
            ->setParameter('query', $identifier)
            ->getOneOrNullResult();

    }
        public function findParticipantBySortie(Sortie $sortie)
    {
        /*$entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT * FROM App\Entity\Sortie s
            INNER JOIN App\Entity\Inscription i ON s.id = i.sortie_id
            INNER JOIN App\Entity\Participant p ON i.participant_id = p.id
            WHERE s.id = :query'
        )
            ->setParameter('query', $id);
            return $query;*/

        //je récupère toutes mes sorties
//        $qb = $this->createQueryBuilder('s');
//        //liaison avec la table inscription
//        $qb->leftJoin('s.inscriptions', 'i');
//        $qb->leftJoin('i.participant', 'p');
//        $qb->Where('i.participant IN (:participant)')
//            ->setParameter('participant', $participant)
//            ->orderBy('s.dateHeureDebut' ,'DESC');
//        $query = $qb->getQuery()->getResult();
//        return $query;
        ///////////////////////
        ///
        $qb = $this->createQueryBuilder('p');


        $qb->innerJoin('p.inscriptions', 'ins')->addSelect('ins');
        $qb->innerJoin('ins.sortie', 's')->addSelect('s');
        $qb->Where("ins.sortie IN (:sortie)");
        $qb->setParameter('sortie', $sortie);

        $query = $qb->getQuery()->getResult();

        return $qb->getQuery()->getResult();
    }



//    /**
//     * @return Participant[] Returns an array of Participant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Participant
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function loadUserByUsername(string $username)
    {
        // TODO: Implement loadUserByUsername() method.
    }
}
