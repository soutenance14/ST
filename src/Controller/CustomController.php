<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class CustomController extends AbstractController
{
    private $parameters;

    public function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $this->parameters = $parameters;
        $this->addParamaters("user", $this->getUser());
        $this->addParamaters("headerImage", "home");
        return parent::render($view, $this->parameters, $response);
    }
    
    public function renderForm(string $view, array $parameters = [], Response $response = null): Response
    {
        $this->parameters = $parameters;
        $this->addParamaters("user", $this->getUser());
        $this->addParamaters("headerImage", "home");
        return parent::renderForm($view, $this->parameters, $response);
    }

    private function addParamaters($indice, $value)
    {
        if(!in_array($indice, $this->parameters))
        {
            $this->parameters[$indice] = $value;
        }
    }
}
