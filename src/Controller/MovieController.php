<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Service\MovieImporter;
use App\Service\OmdbGateway;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        $poster = $gateway->getPoster($movie);

        return $this->render('movie/movieDetail.html.twig', [
            'movie' => $movie,
            'poster' => $poster
        ]);
    }

    /*
     * Displays a movie like it would be saved in DB + poster, but without actually importing it.
     */
    #[Route('preview/{title}', name: 'preview', requirements: ['name' => '\w+'])]
    public function preview(
        string $title,
        MovieImporter $importer,
        OmdbGateway $gateway
    ): Response {
        $movie = $importer->getMovieByTitle($title);
        $poster = $gateway->getPoster($movie);

        return $this->render('movie/movieDetail.html.twig', [
            'movie' => $movie,
            'poster' => $poster
        ]);
    }

    #[Route('create', name:'create')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, MovieRepository $movieRepository): Response
    {
        $form = $this->createForm(MovieType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Movie $movie */
            $movie = $form->getData();
            $movie->setCreatedBy($this->getUser());
            $movieRepository->save($movie, true);

            $this->addFlash('success', 'Movie has been created');
            return $this->redirectToRoute('app_movie_detail', ['id' => $movie->getId()]);
        }

        return $this->render('movie/movieForm.html.twig', [
            'editMode' => 'Create',
            'movieForm' => $form
        ]);
    }

    #[Route('delete/{movie}', name: 'delete')]
    #[IsGranted('remove', 'movie')]
    public function delete(Movie $movie, MovieRepository $repository): Response
    {
        $title = $movie->getTitle();
        $repository->remove($movie, true);

        $this->addFlash('success', "Movie deleted: " . $title);

        return new RedirectResponse('/movie/list');
    }
}
