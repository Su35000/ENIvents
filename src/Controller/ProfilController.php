<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'profil_')]
class ProfilController extends AbstractController
{
    #[Route('', name: 'edit')]
    public function edit(): Response
    {
        return $this->render('profil/edit.html.twig');
    }
}
