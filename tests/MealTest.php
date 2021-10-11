<?php

namespace App\Tests;

use App\DataFixtures\IngredientFixtures;
use App\DataFixtures\ThemeFixtures;
use App\Entity\Meal;
use App\Entity\User;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MealTest extends WebTestCase
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

    public function testMealAccess(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\IngredientFixtures',
            'App\DataFixtures\ThemeFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('Mes plats');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.list"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="home"]');
    }

    public function testMealNavigation(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-plats');
        $this->client->clickLink('Je crée un nouveau plat');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.new"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.list"]');
    }

    public function testMealValidation(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/mes-plats');
        $this->assertCount(0, $crawler->filter('[data-tf="meal.item"]'));

        $crawler = $this->client->clickLink('Je crée un nouveau plat');
        $form = $crawler->filter('[data-tf="meal.form"]')->form();

        $this->client->submit($form);
        $this->assertSelectorTextContains('#meal_name_error .invalid-feedback', 'Merci de donner un nom à votre plat.');
        $this->assertSelectorTextContains('#meal_preparation_error .invalid-feedback', 'Merci d\'indiquer un temps de préparation.');
        $this->assertSelectorTextContains('#meal_ingredients_error .invalid-feedback', 'Merci de sélectionner au moins 1 ingrédient.');

        $form->disableValidation();
        $notExistantUuid = '4d6f5942-3612-426c-bf58-45ea3f87fe70';
        $invalidUuid = '10';
        $form['meal[name]'] = str_repeat('A', 256);
        $form['meal[preparation]'] = 'invalid';
        $form['meal[recurrence]'] = 'invalid';
        $form['meal[ingredients]'] = [$notExistantUuid, $invalidUuid];
        $form['meal[themes]'] = [$notExistantUuid, $invalidUuid];

        $this->client->submit($form);
        $this->assertSelectorTextContains('#meal_name_error .invalid-feedback', 'Le nom de votre plat ne peut pas faire plus de 255 caractères.');
        $this->assertSelectorTextContains('#meal_preparation_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_recurrence_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_ingredients_error .invalid-feedback.err-idx-1', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_ingredients_error .invalid-feedback.err-idx-2', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_themes_error .invalid-feedback.err-idx-1', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_themes_error .invalid-feedback.err-idx-2', 'Le choix sélectionné est invalide.');

        $form['meal[name]'] = 'Pâtes Poulet';
        $form['meal[preparation]'] = Meal::PREP_EXPRESS;
        $form['meal[recurrence]'] = Meal::RECURRENCE_EVERY_WEEK;
        $form['meal[themes]'] = [ThemeFixtures::THEME_DIET_UUID];
        $form['meal[ingredients]'] = [
            IngredientFixtures::INGREDIENT_CHICKEN_UUID,
            IngredientFixtures::INGREDIENT_PASTA_UUID,
        ];

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $preparations = Meal::getPreparations();
        $recurrences = Meal::getRecurrences();
        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(1, $crawler->filter('[data-tf="meal.item"]'));
        $this->assertSelectorTextContains('[data-tf="meal.item.name"]', 'Pâtes Poulet');
        $this->assertSelectorTextContains('[data-tf="meal.item.preparation"]', array_search(Meal::PREP_EXPRESS, $preparations));
        $this->assertSelectorTextContains('[data-tf="meal.item.recurrence"]', array_search(Meal::RECURRENCE_EVERY_WEEK, $recurrences));
        $this->assertSelectorTextContains('[data-tf="meal.item.ingredients"]', 'Escalope de poulet, Pâtes');
        $this->assertSelectorTextContains('[data-tf="meal.item.themes"]', 'Diète maigre');
    }

    public function testMealEdition(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-plats');

        $this->client->clickLink('Modifier');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.edit"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.list"]');

        $crawler = $this->client->clickLink('Modifier');

        $form = $crawler->filter('[data-tf="meal.form"]')->form();
        $form['meal[name]'] = 'Riz Poulet';
        $form['meal[preparation]'] = Meal::PREP_FAST;
        $form['meal[recurrence]'] = Meal::RECURRENCE_TWO_WEEK;
        $form['meal[themes]'] = [ThemeFixtures::THEME_BDIET_UUID];
        $form['meal[ingredients]'] = [
            IngredientFixtures::INGREDIENT_CHICKEN_UUID,
            IngredientFixtures::INGREDIENT_RICE_UUID,
        ];

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $preparations = Meal::getPreparations();
        $recurrences = Meal::getRecurrences();
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="meal.item.name"]', 'Riz Poulet');
        $this->assertSelectorTextContains('[data-tf="meal.item.preparation"]', array_search(Meal::PREP_FAST, $preparations));
        $this->assertSelectorTextContains('[data-tf="meal.item.recurrence"]', array_search(Meal::RECURRENCE_TWO_WEEK, $recurrences));
        $this->assertSelectorTextContains('[data-tf="meal.item.ingredients"]', 'Escalope de poulet, Riz');
        $this->assertSelectorTextContains('[data-tf="meal.item.themes"]', 'Diète grasse');
    }

    public function testMealDel(): void
    {
        $this->login();

        $this->client->request('GET', '/mes-plats');
        $this->client->clickLink('Supprimer');
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.list"]');
        $this->assertCount(0, $crawler->filter('[data-tf="meal.item"]'));
    }
}
