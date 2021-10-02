<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends CustomController
{
/**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();
        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'tricks' => $trickRepository->findAll(),
            'thumbnail_default' => 'test thumbnail'
        ]);
    }

    // /**
    //  * @Route("/", name="home")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('home/index.html.twig', [
    //         'controller_name' => 'HomeController',
    //     ]);
    // }
}
