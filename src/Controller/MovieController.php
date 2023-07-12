<?php

namespace App\Controller;

use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\OmdbGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function detail(int $id, MovieRepository $movieRepository, OmdbGateway $gateway): Response
    {
        $movie = $movieRepository->find($id);

        $poster = 'https://tse4.mm.bing.net/th?id=OIP.xi6JCjV8lRzq4_FX6z5McwHaK-&pid=Api';
        $poster = $gateway->getPoster();


        return $this->render('movie/movieDetail.html.twig', [
            'movie' => $movie,
            'poster' => $poster
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
