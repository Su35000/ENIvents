<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Request $request, LieuRepository $lieuRepository): Response
    {

        $lieu = new Lieu();

        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest(($request));


        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

//            /**
//             * @var Ville $ville
//             */
//
//            $nomVille = $lieuForm->get('ville')->getData();
//            $cpo = $lieuForm->get('cpo')->getData();
//
//            $ville->setNom($nomVille);
//            $ville->setCodePostal($cpo);

            $coordonnees = explode(',',$lieuForm->get('coordonnees')->getData());

            $lieu->setLatitude(floatval($coordonnees[0]));
            $lieu->setLongitude(floatval($coordonnees[1]));

            $lieuRepository->add($lieu, true);

            $this->addFlash('success', "Le lieu a bien été ajouté.");

            return $this->redirectToRoute('sortie_new');
        }

        return $this->render('lieu/new.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request, LieuRepository $lieuRepository): Response
    {

        $lieu = $lieuRepository->find($id);

        if (!$lieu) {
            throw $this->createNotFoundException("O0Oo0PS ! Le lieu n'existe pas !");
        }

        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest(($request));

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {

            $lieuRepository->add($lieu, true);

            $this->addFlash('success', "Le lieu a bien été modifié.");

            return $this->redirectToRoute('main_home', [
                'lieu' => $lieu
            ]);
        }


        return $this->render('lieu/new.html.twig', [
            'lieuForm' => $lieuForm->createView()
        ]);
    }


    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, LieuRepository $lieuRepository): Response
    {

        $lieu = $lieuRepository->find($id);

        //erreur 404
        if (!$lieu) {
            throw $this->createNotFoundException("O0Oo0PS ! Le lieu n'existe pas !");
        }

        return $this->render('lieu/details.html.twig');

    }


}
