<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OmdbGateway
{
    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    public function getPoster(): string
    {
        $url = 'https://www.omdbapi.com/?apikey=e0ded5e2&t=The Matrix';

        $response = $this->httpClient->request('GET', $url);

        $movieData = json_decode($response->getContent(), true);

        return $movieData['Poster'];
    }
}
