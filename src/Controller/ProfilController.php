<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'profil_')]
class ProfilController extends AbstractController
{
    #[Route('/edit', name: 'edit')]
    public function edit(ParticipantRepository $participantRepository, Request $request): Response
    {

        $participant =  $this->getUser();

        $participantId = $participant->getUserIdentifier();
        //Récupération des infos du profil
        $profil = $participantRepository->findOneBy([
            'username' => $participantId
        ]);
        dump($profil);
        dump($participantRepository);
        dump($participantId);

        //Cas d'erreur
        if(!$profil){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }

        $profilForm = $this->createForm(ProfilType::class,$profil);
        $profilForm->handleRequest($request);

        if($profilForm->isSubmitted() && $profilForm->isValid()){
            $participantRepository->add($profil, true);

            $this->addFlash("success", "Profil modifié !");
            return $this->redirectToRoute("profil_edit");
        }

        return $this->render('profil/edit.html.twig', [
            "profilForm" => $profilForm->createView(),
        ]);
    }
}
