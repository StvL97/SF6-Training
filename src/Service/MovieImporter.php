<?php

namespace App\Service;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use DateTime;

class MovieImporter
{
    public function __construct(
        private readonly OmdbGateway     $gateway,
        private readonly MovieRepository $movieRepository,
        private readonly GenreRepository $genreRepository
    ) {
    }

    public function importByTitle(string $title): ?Movie
    {
        $movie = $this->getMovieByTitle($title);

        $this->movieRepository->save($movie, true);

        return $movie;
    }

    public function getMovieByTitle(string $title, $flushGenres = false): ?Movie
    {
        if ($movie = $this->movieRepository->findOneBy(['title' => $title])) {
            dump('found movie');
            return $movie;
        }

        $movieData = $this->gateway->getMovieDataByTitle($title);

        if (array_key_exists('error', $movieData)) {
            return null;
        }

        $releasedAt = new DateTime($movieData['Released']);

        $genres = explode(',', $movieData['Genre']);

        $movie = new Movie();
        $movie
            ->setTitle($title)
            ->setPlot($movieData['Plot'])
            ->setReleasedAt($releasedAt);

        foreach ($genres as $genre) {
            $genre = $this->getGenre($genre);

            $movie->addGenre($genre);
        }

        if ($flushGenres === true) {
            $this->genreRepository->save($genre, true);
        }

        return $movie;
    }

    protected function getGenre(string $name): Genre
    {
        $name = trim(strtolower($name));
        $genre = $this->genreRepository->findOneBy(['name' => $name]);

        if ($genre === null) {
            $genre = new Genre();
            $genre->setName($name);
            $this->genreRepository->save($genre);
        }

        return $genre;
    }
}
