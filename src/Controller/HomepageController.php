<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage', defaults: ['name' => 'World'])]
    #[Route('/hello/{name}', name: 'app_hello', requirements: ['name' => '\w+'])]
    public function index(string $name): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'name' => $name
        ]);
    }
}
