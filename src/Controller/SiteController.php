<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/site', name: 'site_')]
class SiteController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(SiteRepository $siteRepository, Request $request): Response
    {
        $sites = $siteRepository->findAll();

        $site = new Site();

        $siteForm = $this->createForm(SiteType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){

            $siteRepository->add($site, true);

            $this->addFlash("success", "Site modifié !");
            return $this->redirectToRoute("site_list");
        }

        return $this->render('site/list.html.twig', [
            'sites' => $sites,
            'siteForm' => $siteForm->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(SiteRepository $siteRepository, int $id, Request $request): Response
    {
        $site = $siteRepository->find($id);

        //Cas d'erreur
        if(!$site){
            throw $this->createNotFoundException("Erreur : Profil introuvable !");
        }

        $siteForm = $this->createForm(SiteType::class, $site);
        $siteForm->handleRequest($request);

        if($siteForm->isSubmitted() && $siteForm->isValid()){

            $siteRepository->add($site, true);

            $this->addFlash("success", "Site modifié !");
            return $this->redirectToRoute("site_list");
        }

        $sites = $siteRepository->findAll();
        /*        $this->addFlash('success', "Serie removed !");*/

        return $this->render('site/edit.html.twig', [
            'siteForm' => $siteForm->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(SiteRepository $siteRepository, int $id): Response
    {

        $site = $siteRepository->find($id);

        $siteRepository->remove($site, true);

        $sites = $siteRepository->findAll();

        /*        $this->addFlash('success', "Serie removed !");*/

        return $this->redirectToRoute('site_list', [
            'sites' => $sites,
        ]);
    }

    /*#[Route('/add', name: 'add')]
    public function add(SiteRepository $siteRepository, Request $request): Response
    {*/

        /*$site = new Site();

        $siteForm = $this->createForm(SiteType::class, $site);


        $nom = $siteForm->get("nom")->getData();
        $codePostal = $siteForm->get("codePostal")->getData();

        $nom = $request->request->get('nom');
        $codePostal = $request->request->get('codePostal');

        $request->query->get('nom');
        $request->query->get('codePostal');


        $siteForm->handleRequest($request);

        $sites = $siteRepository->findAll();

        if($siteForm->isSubmitted() && $siteForm->isValid()){
            $siteRepository->add($site, true);

            $this->addFlash("success", "Site ajoutée");

            return $this->render('site/edit.html.twig', [
                'siteForm' => $siteForm->createView(),
            ]);
        }

        return $this->redirectToRoute('site_add', [
            'sites' => $sites,
        ]);*/
    /*}*/
}
