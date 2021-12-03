<?php

namespace App\Tests;

class CategoryTest extends BaseTest
{
    public function testAccess(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/');
        $this->client->clickLink('Gestion des catégories');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.list"]');
        $this->assertSelectorNotExists('[data-tf="adm.category.next"]');
        $this->assertSelectorNotExists('[data-tf="adm.category.prev"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="home"]');
    }

    public function testNavigation(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\CategoryFixtures',
        ]);

        $this->login();

        $this->client->request('GET', '/admin/categorie/liste');
        $this->assertSelectorExists('[data-tf="adm.category.next"]');
        $this->assertSelectorNotExists('[data-tf="adm.category.prev"]');

        $this->client->clickLink('Suivant');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.next"]');
        $this->assertSelectorExists('[data-tf="adm.category.prev"]');

        $this->client->request('GET', '/admin/categorie/liste/4');
        $this->assertSelectorNotExists('[data-tf="adm.category.next"]');
        $this->assertSelectorExists('[data-tf="adm.category.prev"]');

        $this->client->clickLink('Précédent');
        $this->assertResponseIsSuccessful();

        $this->client->clickLink('Nouvelle');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.new"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.list"]');
    }

    public function testValidation(): void
    {
        $this->login();

        $crawler = $this->client->request('GET', '/admin/categorie/liste');
        $this->assertCount(2, $crawler->filter('[data-tf="adm.category.item"]'));
        $this->assertSelectorTextContains('[data-tf="adm.category.total"]', '6');

        $crawler = $this->client->clickLink('Nouvelle');
        $form = $crawler->filter('[data-tf="adm.category.form"]')->form();

        $this->client->submit($form);
        $this->assertSelectorTextContains('#category_name_error .invalid-feedback', 'Merci de donner un nom à votre catégorie.');

        $form->disableValidation();
        $form['category[name]'] = str_repeat('A', 256);

        $this->client->submit($form);
        $this->assertSelectorTextContains('#category_name_error .invalid-feedback', 'Le nom de votre catégorie ne peut pas faire plus de 255 caractères.');

        $form['category[name]'] = 'Alcool';
        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="adm.category.total"]', '7');
        $this->assertSelectorTextContains('[data-tf="adm.category.item.name"]', 'Alcool');
    }

    public function testEdition(): void
    {
        $this->login();

        $this->client->request('GET', '/admin/categorie/liste');

        $this->client->clickLink('Modifier');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.edit"]');

        $this->client->clickLink('Retour');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.list"]');

        $crawler = $this->client->clickLink('Modifier');

        $form = $crawler->filter('[data-tf="adm.category.form"]')->form();
        $form['category[name]'] = 'Alcool oui !';

        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('[data-tf="adm.category.item.name"]', 'Alcool oui !');
    }

    public function testDel(): void
    {
        $this->login();

        $this->client->request('GET', '/admin/categorie/liste');
        $this->client->clickLink('Supprimer');
        $this->assertResponseRedirects();

        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="adm.category.list"]');
        $this->assertSelectorTextContains('[data-tf="adm.category.total"]', '6');
    }
}
