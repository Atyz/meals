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
        $this->client->clickLink('Voir la liste de course de cette semaine');
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
        $crawler = $this->client->clickLink('Voir la liste de course de cette semaine');
        $this->assertCount(4, $crawler->filter('[data-tf="shopping.item"]'));

        $lundi = new \DateTime('monday this week');
        $dimanche = new \DateTime('sunday this week');

        $this->assertSelectorTextContains('[data-tf="shopping.now"]', sprintf(
            'Semaine %s du %s au %s',
            $lundi->format('W'),
            $lundi->format('d/m/Y'),
            $dimanche->format('d/m/Y')
        ));

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
        $crawler = $this->client->clickLink('Voir la liste de course de cette semaine');
        $this->client->clickLink('Take');
        $crawler = $this->client->followRedirect();
        $this->assertCount(1, $crawler->filter('[data-tf="shopping.untake"]'));

        $this->client->request('GET', '/');
        $this->generateMenu();

        $crawler = $this->client->clickLink('Voir la liste de course de cette semaine');
        $this->assertCount(0, $crawler->filter('[data-tf="shopping.untake"]'));
    }

    public function testNoMeal()
    {
        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('✘');
        $this->client->followRedirect();

        $this->client->clickLink('Voir la liste de course de cette semaine');
        $this->assertResponseIsSuccessful();
    }
}
