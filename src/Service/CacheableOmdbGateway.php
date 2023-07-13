<?php

namespace App\Service;

use App\Entity\Movie;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Contracts\Cache\CacheInterface;

#[AsDecorator(OmdbGateway::class, priority: 1)]
class CacheableOmdbGateway extends OmdbGateway
{
    public function __construct(
        private readonly OmdbGateway $omdbGateway,
        private readonly CacheInterface $cache
    ) {
    }

    public function getPoster(Movie $movie): string
    {
        $cacheKey = 'movie_poster_' . $movie->getId();

        return $this->cache->get(
            $cacheKey,
            fn() => $this->omdbGateway->getPoster($movie)
        );
    }

    public function getMovieDataByTitle(string $title): array {
        return $this->omdbGateway->getMovieDataByTitle($title);
    }
}
