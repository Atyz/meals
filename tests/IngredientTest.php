<?php

namespace App\Tests;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Ingredient;

class IngredientTest extends BaseTest
{
    public function testAccess(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('Gestion des ingrédients');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.list"]');
        $this->assertSelectorNotExists('[data-tf="adm.ingredient.next"]');
        $this->assertSelectorNotExists('[data-tf="adm.ingredient.prev"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="home"]');
    }

    public function testNavigation(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\CategoryFixtures',
            'App\DataFixtures\IngredientFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/admin/ingredient/liste');
        $this->assertSelectorExists('[data-tf="adm.ingredient.next"]');
        $this->assertSelectorNotExists('[data-tf="adm.ingredient.prev"]');

        $this->client->clickLink('Suivant');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.next"]');
        $this->assertSelectorExists('[data-tf="adm.ingredient.prev"]');

        $this->client->request('GET', '/admin/ingredient/liste/4');
        $this->assertSelectorNotExists('[data-tf="adm.ingredient.next"]');
        $this->assertSelectorExists('[data-tf="adm.ingredient.prev"]');

        $this->client->clickLink('Précédent');
        $this->assertResponseIsSuccessful();

        $this->client->clickLink('Nouveau');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.new"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.list"]');
    }

    public function testValidation(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/ingredient/liste');
        $this->assertCount(2, $crawler->filter('[data-tf="adm.ingredient.item"]'));
        $this->assertSelectorTextContains('[data-tf="adm.ingredient.total"]', '6');

        $crawler = $this->client->clickLink('Nouveau');
        $form = $crawler->filter('[data-tf="adm.ingredient.form"]')->form();

        $form->disableValidation();
        $invalidUuid = '10';
        $form['ingredient[category]'] = $invalidUuid;

        $this->client->submit($form);
        $this->assertSelectorTextContains('#ingredient_name_error .invalid-feedback', 'Merci de donner un nom à votre ingrédient.');
        $this->assertSelectorTextContains('#ingredient_category_error .invalid-feedback', 'Le choix sélectionné est invalide.');

        $form->disableValidation();
        $notExistantUuid = '4d6f5942-3612-426c-bf58-45ea3f87fe70';
        $form['ingredient[name]'] = str_repeat('A', 256);
        $form['ingredient[category]'] = $notExistantUuid;
        $form['ingredient[seasonality]'] = [26];

        $this->client->submit($form);
        $this->assertSelectorTextContains('#ingredient_name_error .invalid-feedback', 'Le nom de votre ingrédient ne peut pas faire plus de 255 caractères.');
        $this->assertSelectorTextContains('#ingredient_category_error .invalid-feedback', 'Le choix sélectionné est invalide.');
        $this->assertSelectorTextContains('#ingredient_seasonality_error .invalid-feedback', 'Le choix sélectionné est invalide.');

        $form['ingredient[name]'] = 'Asperge';
        $form['ingredient[category]'] = CategoryFixtures::CATEGORY_MEAT_UUID;
        $form['ingredient[seasonality]'] = [
            Ingredient::SEASONALITY_SPRING,
            Ingredient::SEASONALITY_SUMMER,
        ];

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="adm.ingredient.total"]', '7');
        $this->assertSelectorTextContains('[data-tf="adm.ingredient.item.name"]', 'Asperge');
    }

    public function testEdition(): void
    {
        $this->login();

        $this->client->request('GET', '/admin/ingredient/liste');

        $this->client->clickLink('Modifier');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.edit"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.list"]');

        $crawler = $this->client->clickLink('Modifier');

        $form = $crawler->filter('[data-tf="adm.ingredient.form"]')->form();
        $form['ingredient[name]'] = 'Asperge oui !';

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="adm.ingredient.item.name"]', 'Asperge oui !');
    }

    public function testDel(): void
    {
        $this->login();

        $this->client->request('GET', '/admin/ingredient/liste');
        $this->client->clickLink('Supprimer');
        $this->assertResponseRedirects();

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.ingredient.list"]');
        $this->assertSelectorTextContains('[data-tf="adm.ingredient.total"]', '6');
    }
}
