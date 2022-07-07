<?php

namespace App\Controller;

use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ville', name: 'ville_')]
class VilleController extends AbstractController
{
    #[Route('/edit', name: 'edit')]
    public function edit(VilleRepository $villeRepository): Response
    {

        $villes = $villeRepository->findAll();

        return $this->render('ville/edit.html.twig', [
            'villes' => $villes,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(VilleRepository $villeRepository, LieuRepository $lieuRepository, int $id): Response
    {

        $ville = $villeRepository->find($id);
//        $lieu = $lieuRepository->findOneBy([
//            'ville_id' => $id
//        ]);
//
//        $lieuRepository->remove($lieu, true);
        $villeRepository->remove($ville, true);

        $villes = $villeRepository->findAll();

/*        $this->addFlash('success', "Serie removed !");*/

        return $this->redirectToRoute('ville_edit', [
                'villes' => $villes,
            ]);
    }


}
