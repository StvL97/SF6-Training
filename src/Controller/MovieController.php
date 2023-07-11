<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('movie/', name:'app_movie_')]
class MovieController extends AbstractController
{
    #[Route('list', name: 'list')]
    public function list(MovieRepository $repository): Response
    {
        $movies = $repository->findAll();

        return $this->render('movie/movieList.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('detail/{id}', name: 'detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, MovieRepository $movieRepository): Response
    {
        $movie = $movieRepository->find($id);

        return $this->render('movie/movieDetail.html.twig', [
            'movie' => $movie
        ]);
    }

    #[Route('create', name:'create')]
    public function create(Request $request, MovieRepository $movieRepository)
    {
        $form = $this->createForm(MovieType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $movie = $form->getData();
            $movieRepository->save($movie, true);

            $this->addFlash('success', 'Movie has been created');
            return $this->redirectToRoute('app_movie_detail', ['id' => $movie->getId()]);
        }

        return $this->render('movie/movieForm.html.twig', [
            'editMode' => 'Create',
            'movieForm' => $form
        ]);
    }
}
