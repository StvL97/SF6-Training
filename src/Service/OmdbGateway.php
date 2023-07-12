<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbGateway
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $movieApi,
        private readonly string $movieApiKey
    ) {
    }

    public function getPoster(string $title): string
    {
        $url = sprintf(
            '%s?apikey=%s&t=%s',
            $this->movieApi,
            $this->movieApiKey,
            $title
        );

        $response = $this->httpClient->request('GET', $url);
        $movieData = json_decode($response->getContent(), true);

        if (!array_key_exists('Poster', $movieData)) {
            return '';
        }

        return $movieData['Poster'];
    }
}
