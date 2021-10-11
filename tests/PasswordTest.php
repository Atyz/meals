<?php

namespace App\Tests;

use App\DataFixtures\UserPasswordFixtures;
use App\Entity\UserPassword;
use App\Repository\UserPasswordRepository;
use App\Service\PasswordManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordTest extends WebTestCase
{
    private KernelBrowser $client;
    protected AbstractDatabaseTool $databaseTool;
    private UserPasswordRepository $userPwdRepo;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();

        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->userPwdRepo = $entityManager->getRepository(UserPassword::class);
    }

    public function testPasswordRequest(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\UserPasswordFixtures',
        ]);

        $this->assertCount(2, $this->userPwdRepo->findAll());

        $this->client->request('GET', '/connexion');
        $this->assertResponseIsSuccessful();

        $this->client->clickLink('Mot de passe oublié ?');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('[data-tf="request-form"]');
        $this->assertSelectorExists('[data-tf="back"]');

        $this->assertCount(1, $this->userPwdRepo->findAll());
    }

    private function testPasswordRequestEmail(string $email, int $countEmails): void
    {
        $crawler = $this->client->request('GET', '/mot-de-passe/demande');

        $form = $crawler->filter('[data-tf="request-form"]')->form();
        $form['request[email]'] = $email;

        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->assertEmailCount($countEmails);

        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="request-success"]');
        $this->assertSelectorExists('[data-tf="back"]');
    }

    public function testPasswordRequestExistingEmail(): void
    {
        $this->testPasswordRequestEmail('atyz@meals.fr', 1);
    }

    public function testPasswordRequestNotExistingEmail(): void
    {
        $this->testPasswordRequestEmail('not-existing@meals.fr', 0);
    }

    public function testPasswordRequestInvalidEmail(): void
    {
        $this->testPasswordRequestEmail('invalid', 0);
    }

    private function testPasswordResetInvalid(string $token, int $errorCode): void
    {
        $this->client->request('GET', '/mot-de-passe/nouveau/'.$token);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="reset-error"]');
        $this->assertSelectorTextContains('[data-tf="reset-error-code"]', $errorCode);
    }

    public function testPasswordResetNotExisting(): void
    {
        $this->testPasswordResetInvalid(
            'not-existing',
            PasswordManager::RESET_ERROR_CODE_NOT_EXISTING
        );
    }

    public function testPasswordResetExpired(): void
    {
        $this->databaseTool->loadFixtures([
            'App\DataFixtures\UserFixtures',
            'App\DataFixtures\UserPasswordFixtures',
        ]);

        $this->testPasswordResetInvalid(
            UserPasswordFixtures::REQUEST_TOKEN_EXPIRED,
            PasswordManager::RESET_ERROR_CODE_EXPIRED
        );
    }

    public function testPasswordReset(): void
    {
        $crawler = $this->client->request('GET', '/mot-de-passe/nouveau/'.UserPasswordFixtures::REQUEST_TOKEN_VALID);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="reset-form"]');
        $this->assertSelectorExists('[data-tf="back"]');

        $form = $crawler->filter('[data-tf="reset-form"]')->form();
        $this->client->submit($form);
        $this->assertSelectorTextContains('#reset_plainPassword_first_error .invalid-feedback', 'Cette valeur ne doit pas être vide.');

        $form['reset[plainPassword][first]'] = '456';
        $form['reset[plainPassword][second]'] = '123';
        $this->client->submit($form);
        $this->assertSelectorTextContains('#reset_plainPassword_first_error .invalid-feedback', 'Les mots de passe doivent être identiques.');

        $form['reset[plainPassword][first]'] = '456';
        $form['reset[plainPassword][second]'] = '456';
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('[data-tf="reset-success"]');
        $this->assertSelectorExists('[data-tf="back"]');

        $this->assertCount(1, $this->userPwdRepo->findAll());
    }
}
