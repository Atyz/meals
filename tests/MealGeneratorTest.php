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

class MealGeneratorTest extends WebTestCase
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
            'App\DataFixtures\IngredientFixtures',
            'App\DataFixtures\ThemeFixtures',
            'App\DataFixtures\MealFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/mes-plats');
        $this->client->clickLink('Je génère plusieurs plats en choisissant les ingrédients');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.generator"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="meal.list"]');
    }

    public function testValidation(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/mes-plats');
        $this->assertCount(3, $crawler->filter('[data-tf="meal.item"]'));

        $crawler = $this->client->clickLink('Je génère plusieurs plats en choisissant les ingrédients');
        $form = $crawler->filter('[data-tf="meal.generator.form"]')->form();

        $this->client->submit($form);
        $this->assertSelectorTextContains('#meal_generate_error .invalid-feedback', 'Merci de sélectionner un ou plusieurs ingrédients dans au moins 2 ensembles.');
        $this->assertSelectorTextContains('#meal_generate_preparation_error .invalid-feedback', 'Merci d\'indiquer un temps de préparation.');

        $form->disableValidation();
        $notExistantUuid = '4d6f5942-3612-426c-bf58-45ea3f87fe70';
        $invalidUuid = '10';
        $form['meal_generate[preparation]'] = 'invalid';
        $form['meal_generate[recurrence]'] = 'invalid';
        $form['meal_generate[ingredient1]'] = [$notExistantUuid, $invalidUuid];
        $form['meal_generate[themes]'] = [$notExistantUuid, $invalidUuid];

        $this->client->submit($form);
        $this->assertSelectorTextContains('#meal_generate_error .invalid-feedback', 'Merci de sélectionner un ou plusieurs ingrédients dans au moins 2 ensembles.');
        $this->assertSelectorTextContains('#meal_generate_preparation_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_generate_recurrence_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_generate_ingredient1_error .invalid-feedback.err-idx-1', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_generate_ingredient1_error .invalid-feedback.err-idx-2', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_generate_themes_error .invalid-feedback.err-idx-1', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#meal_generate_themes_error .invalid-feedback.err-idx-2', 'Le choix sélectionné est invalide.');

        $form['meal_generate[preparation]'] = Meal::PREP_EXPRESS;
        $form['meal_generate[recurrence]'] = Meal::RECURRENCE_EVERY_WEEK;
        $form['meal_generate[themes]'] = [ThemeFixtures::THEME_DIET_UUID];
        $form['meal_generate[ingredient1]'] = [
            IngredientFixtures::INGREDIENT_RICE_UUID,
            IngredientFixtures::INGREDIENT_PASTA_UUID,
        ];
        $form['meal_generate[ingredient2]'] = [
            IngredientFixtures::INGREDIENT_CHICKEN_UUID,
            IngredientFixtures::INGREDIENT_STEAK_UUID,
        ];

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();

        $this->assertCount(4, $crawler->filter('[data-tf="meal.item"]'));

        $this->assertCount(1, $crawler->filter('[data-tf="meal.gen.warn"]'));
        $this->assertCount(3, $crawler->filter('[data-tf="meal.gen.warn.item"]'));
        $this->assertSelectorTextContains('[data-tf="meal.gen.warn.item.name"]', 'Riz Escalope de poulet :');
        $this->assertSelectorTextContains('[data-tf="meal.gen.warn.item.err"]', 'Un plat contenant ces ingrédients existe déjà.');

        $this->assertCount(1, $crawler->filter('[data-tf="meal.gen.succ"]'));
        $this->assertCount(1, $crawler->filter('[data-tf="meal.gen.succ.item"]'));
        $this->assertSelectorTextContains('[data-tf="meal.gen.succ.item.name"]', 'Riz Steak haché');
    }
}
