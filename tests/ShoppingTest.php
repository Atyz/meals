<?php

namespace App\Tests;

use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShoppingTest extends WebTestCase
{
    use MenuTestTrait;

    private KernelBrowser $client;
    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->userRepo = $entityManager->getRepository(User::class);
    }

    private function login(): void
    {
        $atyz = $this->userRepo->findOneByEmail('atyz@meals.fr');
        $this->client->loginUser($atyz);
    }

    public function testAccess(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\MenuFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('Voir la liste de course correspondante');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="shopping.list"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="home"]');
    }

    public function testList()
    {
        $this->login();

        $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Voir la liste de course correspondante');
        $this->assertCount(4, $crawler->filter('[data-tf="shopping.item"]'));

        $this->client->clickLink('Take');
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(3, $crawler->filter('[data-tf="shopping.take"]'));
        $this->assertCount(1, $crawler->filter('[data-tf="shopping.untake"]'));

        $this->client->clickLink('Untake');
        $this->assertResponseRedirects();
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(4, $crawler->filter('[data-tf="shopping.take"]'));
        $this->assertCount(0, $crawler->filter('[data-tf="shopping.untake"]'));
    }

    public function testResetAfterRegenerate()
    {
        $this->login();

        $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Voir la liste de course correspondante');
        $this->client->clickLink('Take');
        $crawler = $this->client->followRedirect();
        $this->assertCount(1, $crawler->filter('[data-tf="shopping.untake"]'));

        $this->regenerateCurrentMenu();

        $crawler = $this->client->clickLink('Voir la liste de course correspondante');
        $this->assertCount(0, $crawler->filter('[data-tf="shopping.untake"]'));
    }
}
