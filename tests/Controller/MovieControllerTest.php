<?php

namespace App\Tests\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @property object $dummyMovie
 * @covers \App\Controller\MovieController
 */
class MovieControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);
        $fixtureExecutor = new ORMExecutor($entityManager);
        $fixtureExecutor->setPurger(new ORMPurger($entityManager));
        $fixtureExecutor->execute([new class extends AbstractFixture {
            public function load(ObjectManager $manager): void
            {
                $movie = new Movie();
                $movie
                    ->setTitle('title 1')
                    ->setPlot('plot 1')
                    ->setReleasedAt(new \DateTime());


                $movie2 = new Movie();
                $movie2
                    ->setTitle('title 2')
                    ->setPlot('plot 2')
                    ->setReleasedAt(new \DateTime());

                $this->addReference('dummy movie', $movie);
                $this->addReference('dummy movie 2', $movie2);

                $manager->persist($movie);
                $manager->persist($movie2);
                $manager->flush();
            }
        }]);

        $this->dummyMovie = $fixtureExecutor->getReferenceRepository()->getReference('dummy movie', Movie::class);
        self::ensureKernelShutdown();
    }

    public function testItDisplaysMovies(): void
    {
        $client = static::createClient();
        $client->request('GET', 'movie/list');

        $this->assertSelectorCount(2, '.movie-list ul li');
    }

    public function testItDisplaysTheDetailsOfAMovie(): void
    {
        $client = static::createClient();

        $client->request('GET', 'movie/detail/' .  $this->dummyMovie->getId());

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('td.movie-plot', 'The movie plot is not visible');
        $this->assertSelectorTextContains('td.movie-plot', $this->dummyMovie->getPlot(), 'plot content');
        $this->assertSelectorExists('td.movie-released-at', 'released at');
    }
//
    public function testItCanCreateANewMovie(): void
    {
        $client = static::createClient();

        $client->request(method:'POST', uri:'movie/create', content:json_encode(['movie' => [
            'title' => 'foo',
            'plot' => 'some plot',
            'releasedAt' => new \DateTime(),
            '_token' => 'xxxxxx'
        ]]));
        $movieRepository = $this->getContainer()->get(MovieRepository::class);

        /** @var MovieRepository $movieRepository */
        $movie = $movieRepository->findOneBy(['title' => 'foo']);
        self::assertEquals('some plot', $movie->getPlot(), 'Plot is not as expected');
    }
}
