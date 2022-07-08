<?php

namespace App\Controller;

use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'ville_')]
class VilleController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(VilleRepository $villeRepository): Response
    {

        $villes = $villeRepository->findAll();

        return $this->render('ville/list.html.twig', [
            'villes' => $villes,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(VilleRepository $villeRepository, int $id, Request $request): Response
    {
        $ville = $villeRepository->find($id);

        //Cas d'erreur
        if(!$ville){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }

        $villeForm = $this->createForm(VilleType::class, $ville);
        $villeForm->handleRequest($request);

        if($villeForm->isSubmitted() && $villeForm->isValid()){

            $villeRepository->add($ville, true);

            $this->addFlash("success", "Ville modifié !");
            return $this->redirectToRoute("ville_list");
        }

        $villes = $villeRepository->findAll();
        /*        $this->addFlash('success', "Serie removed !");*/

        return $this->render('ville/edit.html.twig', [
            'villeForm' => $villeForm->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(VilleRepository $villeRepository, LieuRepository $lieuRepository, int $id): Response
    {

        $ville = $villeRepository->find($id);

        $villeRepository->remove($ville, true);

        $villes = $villeRepository->findAll();

/*        $this->addFlash('success', "Serie removed !");*/

        return $this->redirectToRoute('ville_list', [
                'villes' => $villes,
            ]);
    }


}
