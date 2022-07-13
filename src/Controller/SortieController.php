<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CancelSortieType;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\InscriptionRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\ParticipantRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{

    #[Route('', name: 'home')]
    public function home(Request $request, SortieRepository $sortieRepository): Response
    {
        /**
         *
         * @var Participant $user
         */
        $user = $this->getUser();

        $searchSortieForm = $this->createForm(SearchSortieType::class);
        $searchSortieForm->handleRequest($request);

        $valeurSaisie = $searchSortieForm->get('le_nom_de_la_sortie_contient')->getData();
        $dateDebut = $searchSortieForm->get('entre')->getData();
        $dateFin = $searchSortieForm->get('et')->getData();
        $filtreOrga = $searchSortieForm->get('filtreOrga')->getData();
        $filtreInscrit = $searchSortieForm->get('filtreInscrit')->getData();
        $filtrePasInscrit = $searchSortieForm->get('filtrePasInscrit')->getData();
        $filtreSortiesPasse = $searchSortieForm->get('filtreSortiesPasse')->getData();


        if ($searchSortieForm->isSubmitted() && $searchSortieForm->isValid()) {

            $sortiesRecherchees = $sortieRepository->findByFilters($valeurSaisie,$dateDebut,$dateFin,$filtreOrga,$filtreInscrit,$filtrePasInscrit,$filtreSortiesPasse,$user);
           // dd($sortiesRecherchees);
//            $sortiesRecherchees = array();
//
//            if (isset($valeurSaisie)){
//                $sortiesRecherchees = array_merge_recursive($sortiesRecherchees,$sortieRepository->findByFilters($valeurSaisie));
//            }
//
//            if ($searchSortieForm->get('filtreOrga')->getData() == true){
//                $sortiesOrga =$sortieRepository->findAllSortiesOrganiseesPar($user);
//                $sortiesRecherchees = array_merge_recursive($sortiesRecherchees,$sortiesOrga);
//            }
//
//            if ($searchSortieForm->get('filtreInscrit')->getData() == true){
//                $sortiesRecherchees = array_merge_recursive($sortiesRecherchees,$sortieRepository->findAllSortiesParticipeesPar($user));
//            }

            return $this->render('sortie/home.html.twig', [
                'searchSortieForm' => $searchSortieForm->createView(),
                'sorties' => $sortiesRecherchees
            ]);
        }

        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/home.html.twig', [
            'searchSortieForm' => $searchSortieForm->createView(),
            'sorties' => $sorties
        ]);
    }


    #[Route('/new', name: 'new')]
    public function new(Request $request, SortieRepository $sortieRep, EtatRepository $etatRep): Response
    {
        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new DateTime());
        $sortie->setDateCloture(new DateTime());

        /**
         *
         * @var Participant $user
         */
        $user = $this->getUser();


        $etat = $etatRep->find(2);
        $etat->setLibelle('cree');

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortie->setOrganisateur($user);
            $sortie->setEtat($etat);

            $sortieRep->add($sortie, true);

            $this->addFlash('success', "L'évènement a bien été crée");

            return $this->redirectToRoute('sortie_details', [
                'id' => $sortie->getId()
            ]);
        }

        return $this->render('sortie/new.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, SortieRepository $sortieRep, ParticipantRepository $participantRepository): Response
    {


        $sortie = $sortieRep->find($id);
        $participants = $participantRepository->findParticipantBySortie($sortie);
        //erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException("O0Oo0PS ! La sortie n'existe pas !");
        }

        return $this->render('sortie/details.html.twig', [
            'participants'=>$participants,
            'sortie'=>$sortie
        ]);

    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, SortieRepository $sortieRep, Request $request): Response
    {

        $sortie = $sortieRep->find($id);

        //erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException("O0Oo0PS ! La sortie n'existe pas !");
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortieRep->add($sortie, true);

            $this->addFlash('success', "L'évènement a bien été modifiée");

            return $this->redirectToRoute('sortie_details', [
                'id' => $sortie->getId()
            ]);
        }
        return $this->render('sortie/edit.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/enlist/{id}', name: 'enlist')]
    public function enlist(int $id, Request $request, SortieRepository $sortieRep, ParticipantRepository $participantRepository, InscriptionRepository $inscriptionRepository): Response
    {
        $inscription = new Inscription();

        $participant = $this->getUser();

        //dump($participant);


        $sortie = $sortieRep->find($id);

        if (in_array($participant, $participantRepository->findParticipantBySortie($sortie))){
            $this->addFlash('success', ' Vous êtes déjà inscrit à la sortie !');

            return $this->redirectToRoute('sortie_home');
        }

        $today = new DateTime();

       // dd($sortie->getDateCloture());

        if ($today>$sortie->getDateCloture()){
            $this->addFlash('success', "Date limite d'inscription dépassée, essayez une prochaine fois ... !");

            return $this->redirectToRoute('sortie_home');
        }

        $inscription->setParticipant($participant);
        $inscription->setSortie($sortie);

        if (!$participant) {
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }
        $inscription->setDateInscription(new DateTime());
        $inscriptionRepository->add($inscription, true);

        $this->addFlash('success', ' Vous êtes inscrit à la sortie !');


        return $this->redirectToRoute('sortie_home', [
            "user" => $participant
        ]);

    }

    #[Route('/annulation/{id}', name: 'annulation')]
    public function annulation(int $id, Request $request, SortieRepository $sortieRep, EtatRepository $etatRepository): Response
    {
        $sortie = $sortieRep->find($id);


        //erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException("O0Oo0PS ! La sortie n'existe pas !");
        }

        $sortieForm = $this->createForm(cancelSortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $etat = $etatRepository->find(6);
            dump($etat);

            $sortie->setEtat($etat);

            $sortieRep->add($sortie, true);

            $this->addFlash('success', "L'évènement a bien été annulé");

            return $this->redirectToRoute('sortie_details', [
                'id' => $sortie->getId()
            ]);
        }

        return $this->render('sortie/annulation.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }


}
