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
        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'tricks' => $trickRepository->findDesc(),
            // 'tricks' => null,
            'user' => $this->getUser(),
            'thumbnail_default' => 'test thumbnail'
        ]);
    }
}
