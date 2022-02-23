<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220223161825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_day_ingredient (menu_day_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', ingredient_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_54E50D8BDA8519FE (menu_day_id), INDEX IDX_54E50D8B933FE08C (ingredient_id), PRIMARY KEY(menu_day_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu_day_ingredient ADD CONSTRAINT FK_54E50D8BDA8519FE FOREIGN KEY (menu_day_id) REFERENCES menu_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE menu_day_ingredient ADD CONSTRAINT FK_54E50D8B933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE menu_day_ingredient');
    }
}
