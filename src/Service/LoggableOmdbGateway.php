<?php

namespace App\Service;

use App\Entity\Movie;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(OmdbGateway::class, priority: 0)]
class LoggableOmdbGateway extends OmdbGateway
{
    public function __construct(
        private readonly OmdbGateway $omdbGateway,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getPoster(Movie $movie): string
    {
        $poster = $this->omdbGateway->getPoster($movie);

        $this->logger->info($poster);

        return $poster;
    }
}
