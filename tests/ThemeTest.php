<?php

namespace App\Tests;

use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThemeTest extends WebTestCase
{
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
            'App\DataFixtures\UserFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('Mes thèmes');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="theme.list"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="home"]');
    }

    public function testNavigation(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-themes');
        $this->client->clickLink('Je crée un nouveau thème');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="theme.new"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="theme.list"]');
    }

    public function testValidation(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/mes-themes');
        $this->assertCount(0, $crawler->filter('[data-tf="theme.item"]'));

        $crawler = $this->client->clickLink('Je crée un nouveau thème');
        $form = $crawler->filter('[data-tf="theme.form"]')->form();

        $this->client->submit($form);
        $this->assertSelectorTextContains('#theme_name_error .invalid-feedback', 'Merci de donner un nom à votre thème.');

        $form->disableValidation();
        $form['theme[name]'] = str_repeat('A', 256);
        $this->client->submit($form);
        $this->assertSelectorTextContains('#theme_name_error .invalid-feedback', 'Le nom de votre thème ne peut pas faire plus de 255 caractères.');

        $form['theme[name]'] = 'Nouveau thème';
        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-tf="theme.item"]'));
        $this->assertSelectorTextContains('[data-tf="theme.item.name"]', 'Nouveau thème');
    }

    public function testEdition(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-themes');

        $this->client->clickLink('Modifier');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="theme.edit"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="theme.list"]');

        $crawler = $this->client->clickLink('Modifier');

        $form = $crawler->filter('[data-tf="theme.form"]')->form();
        $form['theme[name]'] = 'Thème modifié';

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="theme.item.name"]', 'Thème modifié');
    }

    public function testDel(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-themes');
        $this->client->clickLink('Supprimer');
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="theme.list"]');
        $this->assertCount(0, $crawler->filter('[data-tf="theme.item"]'));
    }
}
