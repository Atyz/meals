<?php

namespace App\Tests;

use App\DataFixtures\WeekFixtures;
use App\Entity\Meal;
use App\Entity\User;
use App\Service\MenuNavigator;
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

        $this->client->clickLink('Générer le menu de la semaine');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="menu.generate"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();

        $this->client->clickLink('Semaine précédente');
        $this->assertResponseIsSuccessful();
        $this->client->clickLink('Semaine suivante');
        $this->assertResponseIsSuccessful();

        $lundi = new \DateTime('monday this week');
        $dimanche = new \DateTime('sunday this week');

        $this->assertSelectorTextContains('[data-tf="menu.now"]', sprintf(
            'Semaine %s du %s au %s',
            $lundi->format('W'),
            $lundi->format('d/m/Y'),
            $dimanche->format('d/m/Y')
        ));

        $this->client->request('GET', '/semaine-2000-01-01');
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('[data-tf="menu.now"]');

        $maxPrevWeek = (new \DateTime(MenuNavigator::FIRST_DAY_OF_WEEK.' this week'))
            ->modify('- '.MenuNavigator::MAX_WEEK_PREV.' weeks');

        $crawler = $this->client->request('GET', '/semaine-'.$maxPrevWeek->format('Y-m-d'));
        $this->assertCount(0, $crawler->filter('[data-tf="menu.prev.week"]'));
        $this->assertCount(0, $crawler->filter('[data-tf="menu.link"]'));

        $maxNextWeek = (new \DateTime(MenuNavigator::FIRST_DAY_OF_WEEK.' this week'))
            ->modify('+ '.MenuNavigator::MAX_WEEK_NEXT.' weeks');

        $crawler = $this->client->request('GET', '/semaine-'.$maxNextWeek->format('Y-m-d'));
        $this->assertCount(0, $crawler->filter('[data-tf="menu.next.week"]'));
        $this->assertCount(1, $crawler->filter('[data-tf="menu.link"]'));
    }

    public function testValidation(): void
    {
        $this->login();

        $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Générer le menu de la semaine');
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

        $this->client->request('GET', '/');
        $crawler = $this->generateMenu();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="menu.now"]');
        $this->assertCount(14, $crawler->filter('[data-tf="menu.item"]'));
    }

    public function testChangeMeal(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/');
        $prevName = $crawler->filter('[data-tf="menu.item.name"]')->text();
        $this->client->clickLink('♺');
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $name = $crawler->filter('[data-tf="menu.item.name"]')->text();
        $this->assertResponseIsSuccessful();
        $this->assertNotEquals($prevName, $name);

        $this->client->clickLink('Semaine suivante');
        $prevWeek = $crawler->filter('[data-tf="menu.week"]')->text();
        $this->generateMenu(WeekFixtures::WEEK_EMPTY_UUID);
        $this->assertSelectorTextContains('[data-tf="menu.item.name"]', 'Aucun repas sélectionné !');
        $this->client->clickLink('♺');
        $crawler = $this->client->followRedirect();
        $this->assertSelectorTextContains('[data-tf="menu.item.name"]', 'Aucun repas sélectionné !');
        $this->assertSelectorTextContains('[data-tf="menu.week"]', $prevWeek);
    }
}
