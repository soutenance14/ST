<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

abstract class CustomController extends AbstractController
{
    public function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters["user"] = $this->getUser();
        if(!in_array("headerImage", $parameters))
        {
            $parameters["headerImage"] = "home";
        }
        return parent::render($view, $parameters, $response);
    }
    
    public function renderForm(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters["user"] = $this->getUser();
        if(!in_array("headerImage", $parameters))
        {
            $parameters["headerImage"] = "home";
        }
        return parent::renderForm($view, $parameters, $response);
    }

}
