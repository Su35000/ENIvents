<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;


#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('', name: 'home')]
    public function home(): Response
    {
        return $this->render('sortie/home.html.twig');
    }

    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, SortieRepository $sortieRep): Response
    {

        $sortie = $sortieRep->find($id);

        return $this->render('sortie/details.html.twig');
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, SortieRepository $sortieRep): Response
    {
        $sortie = new Sortie();

        /**
         *
         * @var Participant $user
         */
       $user = $this->getUser();

        /**
         *
         * @var Etat $etat
         */
        $etat = new Etat();
        $etat->setLibelle('cree');

        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $sortie->setOrganisateur($user);
            $sortie->setEtat($etat);

            $sortieRep->add($sortie, true);

            $this->addFlash('success', "L'évènement à bien été crée");

            return  $this->redirectToRoute('sortie_details',[
                'id'=>$sortie->getId()
            ]);
        }


        return $this->render('sortie/new.html.twig', [
            'sortieForm' => $sortieForm->createView()
        ]);
    }

}
