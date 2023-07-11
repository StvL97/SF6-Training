<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('movies/', name:'app_movie_')]
class MovieController extends AbstractController
{
    #[Route('list', name: 'list')]
    public function index(): Response
    {
        $movies = [
            'The Matrix',
            'Lord of the rings',
            'Maze runner'
        ];

        return $this->render('movie/movieList.html.twig', [
            'movies' => $movies,
        ]);
    }
}
