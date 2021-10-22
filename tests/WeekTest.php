<?php

namespace App\Tests;

use App\DataFixtures\ThemeFixtures;
use App\Entity\Meal;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WeekTest extends WebTestCase
{
    private KernelBrowser $client;
    protected AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->mealRepo = $entityManager->getRepository(Meal::class);
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
            'App\DataFixtures\ThemeFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('Mes semaines types');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="week.list"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="home"]');
    }

    public function testNavigation(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-semaines-types');
        $this->client->clickLink('Je crée une nouvelle semaine type');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="week.new"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="week.list"]');
    }

    public function testValidation(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/mes-semaines-types');
        $this->assertCount(0, $crawler->filter('[data-tf="week.item"]'));

        $crawler = $this->client->clickLink('Je crée une nouvelle semaine type');
        $form = $crawler->filter('[data-tf="week.form"]')->form();

        $this->client->submit($form);
        $this->assertSelectorTextContains('#week_name_error .invalid-feedback', 'Merci de donner un nom à votre semaine type.');
        $this->assertSelectorTextContains('#week_error .invalid-feedback', 'Vous devez sélectionner au moins un repas.');

        $form->disableValidation();
        $notExistantUuid = '4d6f5942-3612-426c-bf58-45ea3f87fe70';
        $invalidUuid = '10';
        $form['week[name]'] = str_repeat('A', 256);
        $form['week[days][11][themes]'] = [$notExistantUuid, $invalidUuid];
        $form['week[days][11][preparations]'] = ['invalid'];

        $this->client->submit($form);
        $this->assertSelectorTextContains('#week_name_error .invalid-feedback', 'Le nom de votre semaine type ne peut pas faire plus de 255 caractères.');
        $this->assertSelectorTextContains('#week_days_11_preparations_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#week_days_11_themes_error .invalid-feedback.err-idx-1', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#week_days_11_themes_error .invalid-feedback.err-idx-2', 'Le choix sélectionné est invalide.');

        $form['week[name]'] = 'Ma semaine type 1';
        $form['week[days][11][themes]'] = [ThemeFixtures::THEME_DIET_UUID];
        $form['week[days][11][preparations]'] = [Meal::PREP_EXPRESS];

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-tf="week.item"]'));
        $this->assertSelectorTextContains('[data-tf="week.item.name"]', 'Ma semaine type 1');
    }

    public function testEdition(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-semaines-types');

        $this->client->clickLink('Modifier');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="week.edit"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="week.list"]');

        $crawler = $this->client->clickLink('Modifier');

        $form = $crawler->filter('[data-tf="week.form"]')->form();
        $form['week[name]'] = 'Ma semaine type modifiée';

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="week.item.name"]', 'Ma semaine type modifiée');
    }

    public function testDel(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-semaines-types');
        $this->client->clickLink('Supprimer');
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="week.list"]');
        $this->assertCount(0, $crawler->filter('[data-tf="week.item"]'));
    }
}
