<?php

namespace App\Controller;

use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'profil_')]
class ParticipantController extends AbstractController
{
    #[Route('/edit', name: 'edit')]
    public function edit(UserPasswordHasherInterface $userPasswordHasher, ParticipantRepository $participantRepository, Request $request): Response
    {

        $participant =  $this->getUser();

        $participantId = $participant->getUserIdentifier();
        //Récupération des infos du profil
        $profil = $participantRepository->findOneBy([
            'username' => $participantId
        ]);

        //Cas d'erreur
        if(!$profil){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }

        $profilForm = $this->createForm(ProfilType::class,$profil);
        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted() && $profilForm->isValid()){
            $participant->setPassword(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $profilForm->get('password')->getData()
                )
            );
            $participantRepository->add($profil, true);

            $this->addFlash("success", "Profil modifié !");
            return $this->redirectToRoute("profil_edit");
        }

        return $this->render('profil/edit.html.twig', [
            "profilForm" => $profilForm->createView(),
        ]);
    }

    #[Route('/details', name: 'details')]
    public function details(UserPasswordHasherInterface $userPasswordHasher, SortieRepository $sortieRep, ParticipantRepository $participantRepository, Request $request): Response
    {

        $participant =  $this->getUser();

        $participantId = $participant->getUserIdentifier();
        //Récupération des infos du profil
        $profil = $participantRepository->findOneBy([
            'username' => $participantId
        ]);



        $sortiesParticipees = $sortieRep->findAllSortiesParticipeesPar($participant);
        $sortiesOrganisees = $sortieRep->findAllSortiesOrganiseesPar($participant);

        //Cas d'erreur
        if(!$profil){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }


        return $this->render('profil/details.html.twig', [
            "user" => $participant,
            "sortiesParticipees" => $sortiesParticipees,
            "sortiesOrganisees" => $sortiesOrganisees
        ]);
    }


    #[Route('/details/{id}', name: 'detailsById')]
    public function detailsById(int $id,UserPasswordHasherInterface $userPasswordHasher, ParticipantRepository $participantRepository, Request $request): Response
    {

        $participant = $participantRepository->find($id);

        $participantId = $participant->getUserIdentifier();
        //Récupération des infos du profil
        $profil = $participantRepository->findOneBy([
            'username' => $participantId
        ]);

        //Cas d'erreur
        if(!$profil){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }


        return $this->render('profil/details-organisateur.html.twig', [
            "user" => $participant,
        ]);
    }
}
