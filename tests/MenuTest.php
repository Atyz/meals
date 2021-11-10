<?php

namespace App\Tests;

use App\DataFixtures\WeekFixtures;
use App\Entity\Meal;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MenuTest extends WebTestCase
{
    use MenuTestTrait;

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

    public function testNavigation(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\IngredientFixtures',
            'App\DataFixtures\ThemeFixtures',
            'App\DataFixtures\MealFixtures',
            'App\DataFixtures\WeekFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->assertSelectorExists('[data-tf="menu.none"]');
        $this->assertSelectorExists('[data-tf="menu.link"]');

        $this->client->clickLink('Générer une semaine');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="menu.generate"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
    }

    public function testValidation(): void
    {
        $this->login();

        $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Générer une semaine');
        $form = $crawler->filter('[data-tf="menu.form"]')->form();

        $form->disableValidation();
        $notExistantUuid = '4d6f5942-3612-426c-bf58-45ea3f87fe70';
        $form['menu[week]'] = $notExistantUuid;
        $form['menu[date]'] = 'invalid';
        $this->client->submit($form);
        $this->assertSelectorTextContains('#menu_week_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#menu_date_error .invalid-feedback', 'Le choix sélectionné est invalide.');

        $invalidUuid = '10';
        $form['menu[week]'] = $invalidUuid;
        $form['menu[date]'] = 0;
        $this->client->submit($form);
        $this->assertSelectorTextContains('#menu_week_error .invalid-feedback', 'Le choix sélectionné est invalide.');

        $form['menu[week]'] = WeekFixtures::WEEK_CLASSIC_UUID;
        $form['menu[date]'] = 0;
        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="menu.now"]');
        $this->assertCount(14, $crawler->filter('[data-tf="menu.item"]'));
    }

    public function testRegenerate(): void
    {
        $this->login();

        $crawler = $this->regenerateCurrentMenu();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="menu.now"]');
        $this->assertCount(14, $crawler->filter('[data-tf="menu.item"]'));
    }
}
