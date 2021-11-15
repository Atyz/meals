<?php

namespace App\Tests;

use App\DataFixtures\WeekFixtures;

trait MenuTestTrait
{
    public function regenerateCurrentMenu()
    {
        $this->client->request('GET', '/');
        $crawler = $this->client->clickLink('Générer le menu de la semaine');
        $form = $crawler->filter('[data-tf="menu.form"]')->form();

        $form['menu[week]'] = WeekFixtures::WEEK_CLASSIC_UUID;
        $form['menu[date]'] = 0;
        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        return $crawler;
    }
}
