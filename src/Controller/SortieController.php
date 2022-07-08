<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Form\SortieType;
use App\Repository\InscriptionRepository;
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
        $searchSortieForm = $this->createForm(SearchSortieType::class);
        $searchSortieForm->handleRequest($request);

        $sorties = $sortieRepository->findAll();

//        var_dump($sorties);

        return $this->render('sortie/home.html.twig', [
            'searchSortieForm' => $searchSortieForm->createView(),
            'sorties' => $sorties
        ]);
    }


    #[Route('/new', name: 'new')]
    public function new(Request $request, SortieRepository $sortieRep): Response
    {
        $sortie = new Sortie();
        $sortie->setDateHeureDebut(new DateTime());
        $sortie->setDateCloture(new DateTime());

        /**
         *
         * @var Participant $user
         */
        $user = $this->getUser();


        $etat = new Etat();
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
    public function details(int $id, SortieRepository $sortieRep): Response
    {

        $sortie = $sortieRep->find($id);

        //erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException("O0Oo0PS ! La sortie n'existe pas !");
        }

        return $this->render('sortie/details.html.twig');

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
        return $this->render('sortie/edit.html.twig',[
            'sortieForm' => $sortieForm->createView()
        ]);
    }

    #[Route('/enlist/{id}', name: 'enlist')]
    public function enlist(int $id, Request $request, SortieRepository $sortieRep,ParticipantRepository $participantRepository, InscriptionRepository $inscriptionRepository): Response
    {
        $inscription = new Inscription();

        /*$participant = $participantRepository->find($id);*/
        /*$participant = $this->getUser();*/
        /**
         *
         * @var Participant $user
         */
        $participant = $this->getUser();

        dump($participant);

    /*    $participant->getUserIdentifier();

       $profil = $participantRepository->findOneBy([
            'username' => $participant
        ]);*/

        $sortie = $sortieRep->find($id);

        $inscription->setParticipant($participant);
        $inscription->setSortie($sortie);

        /*dump($profil);*/

/*        if(!$profil){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }*/



            $inscription->setDateInscription(new DateTime());
            $inscriptionRepository->add($inscription, true);




        return $this->render('sortie/enlist.html.twig', [
            "user" => $participant
        ]);
    }

}
