<?php

namespace App\Tests;

use App\DataFixtures\WeekFixtures;

trait MenuTestTrait
{
    public function generateMenu($weekUuid = WeekFixtures::WEEK_CLASSIC_UUID, $weekChoice = 0)
    {
        $crawler = $this->client->clickLink('Générer le menu de la semaine');
        $form = $crawler->filter('[data-tf="menu.form"]')->form();

        $form['menu[week]'] = $weekUuid;
        $form['menu[date]'] = $weekChoice;
        $this->client->submit($form);
        $this->assertResponseRedirects();

        $crawler = $this->client->followRedirect();

        return $crawler;
    }
}
