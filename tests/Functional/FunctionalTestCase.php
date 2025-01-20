<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    protected function login(string $email = 'ina@zaoui.com'): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        self::assertNotNull($user);

        $this->client->loginUser($user);
    }
}
