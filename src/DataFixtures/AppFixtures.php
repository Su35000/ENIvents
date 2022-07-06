<?php

namespace App\DataFixtures;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{

    private Generator $generator;
    private ObjectManager $manager;
    private UserPasswordHasherInterface $passwordHasher;


    public function load(ObjectManager $manager): void
    {

        $this->manager = $manager;

        $this->ajoutParticipants();
        $this->ajoutSite();
        $this->ajoutSortie();


        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->generator = Factory::create('fr_FR');
        $this->passwordHasher = $passwordHasher;
    }

    private function ajoutParticipant()
    {
        $participant = new Participant();
        $participant->setNom($this->generator->lastName)
            ->setPrenom($this->generator->firstName)
            ->setUsername($this->generator->userName)
            ->setEmail($this->generator->email)
            ->setRoles(['ROLE_ORGANISATEUR'])     //["ROLE_ORGANISATEUR"]
            ->setTelephone($this->generator->phoneNumber)
            ->setActif(true)
            ->setSite($this->ajoutSite());

        $password = $this->passwordHasher->hashPassword($participant, "123456");
        $participant->setPassword($password);

        $this->manager->persist($participant);
        $this->manager->flush();

        ;




        return $participant;
    }


    private function ajoutParticipants()
    {

        for ($i = 0; $i < 10; $i++) {
            $participant = new Participant();
            $participant->setNom($this->generator->lastName)
                ->setPrenom($this->generator->firstName)
                ->setUsername($this->generator->userName)
                ->setEmail($this->generator->email)
                ->setRoles(['ROLE_PARTICIPANT'])   //["ROLE_PARTICIPANT"]
                ->setTelephone($this->generator->phoneNumber)
                ->setActif(true)
                ->setSite($this->ajoutSite());
            $password = $this->passwordHasher->hashPassword($participant, "123456");
            $participant->setPassword($password);

            $this->manager->persist($participant);
        }
        $this->manager->flush();
    }

    private function ajoutSite()
    {

        $site = new Site();
        $site->setNom($this->generator->randomElement(['Nantes', 'Rennes', 'Quimper', 'Niort']));
        $this->manager->persist($site);
        $this->manager->flush();
        return $site;
    }


    private function ajoutSortie(){

        $sortie = new Sortie();
        $sortie->setOrganisateur($this->ajoutParticipant())
            ->setLieu($this->ajoutLieu())
            ->setEtat($this->ajoutEtat())
            ->setNom($this->generator->randomElement(['Découverte des plantes sauvages','Les Nuits Celtiques','Randonnée et Cueillette', 'Balade contée à vélo','Apéro Klam',' Escape Game','Cours de cuisine', 'Laser Game', 'Grosse Murge', 'Paint-ball','Accrobranche','Randonnée en Quad','Simulateur de chute-libre','WarpZone Rennes','Picnic au parc', 'Apéro Défonce', 'Restaurant Pizza', 'Concert métal', 'Speed Dating', 'Sortie bien être', 'Initiation Yoga', 'Boeuf jazz', 'Jam Session', 'Poker', 'Nuit : classiques du cinéma']))
            ->setDateHeureDebut($this->generator->dateTime)
            ->setDateCloture($this->generator->dateTime)
            ->setDuree($this->generator->numberBetween(60,120))
            ->setNbInscritMax($this->generator->numberBetween(2,50))
            ->setDescription($this->generator->words(50, true));
    }

    private function ajoutLieu(){

        $lieu = new Lieu();
        $lieu->setVille($this->ajoutVille())
            ->setNom($this->generator->domainName)
            ->setLatitude($this->generator->latitude)
            ->setLongitude($this->generator->longitude)
            ->setRue($this->generator->streetName);
        $this->manager->persist($lieu);
        $this->manager->flush();
        return $lieu;

    }

    private function ajoutVille(){

        $ville = new Ville();
        $ville->setNom($this->generator->city)
            ->setCodePostal($this->generator->postcode);
        $this->manager->persist($ville);
        $this->manager->flush();
        return $ville;
    }

    private function ajoutEtat(){
        $etat = new Etat();
        $etat->setLibelle($this->generator->randomElement(['cree','ouverte','cloturee','en_cours', 'passee','annulee']));
        $this->manager->persist($etat);
        $this->manager->flush();
        return $etat;
    }

}

