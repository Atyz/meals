<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109220021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shopping (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', menu_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ingredient_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status TINYINT(1) NOT NULL, INDEX IDX_FB45F439CCD7E912 (menu_id), INDEX IDX_FB45F439933FE08C (ingredient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shopping ADD CONSTRAINT FK_FB45F439CCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE shopping ADD CONSTRAINT FK_FB45F439933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE shopping');
    }
}
