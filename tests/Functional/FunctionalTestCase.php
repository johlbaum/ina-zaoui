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

    /**
     * Configuration initiale pour chaque test.
     * Initialise le client HTTP et l'EntityManager.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    /**
     * Effectue une requête HTTP GET sur une URI donnée.
     *
     * @param string $uri        : l'URI à laquelle envoyer la requête GET
     * @param array  $parameters : les paramètres de la requête (optionnels)
     *
     * @return Crawler : le crawler contenant la réponse de la requête
     */
    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    /**
     * Effectue une requête HTTP DELETE sur une URI donnée.
     *
     * @param string $uri        : l'URI à laquelle envoyer la requête DELETE
     * @param array  $parameters : les paramètres de la requête (optionnels)
     *
     * @return Crawler : le crawler contenant la réponse de la requête
     */
    protected function delete(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('DELETE', $uri, $parameters);
    }

    /**
     * Connecte un utilisateur en fonction de son adresse e-mail.
     *
     * @param string $email : l'adresse e-mail de l'utilisateur à connecter (par défaut : 'ina@zaoui.com').
     */
    protected function login(string $email = 'ina@zaoui.com'): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        self::assertNotNull($user, sprintf('Aucun utilisateur trouvé avec l\'email "%s".', $email));

        $this->client->loginUser($user);
    }
}
