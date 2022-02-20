<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220113165630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_day DROP FOREIGN KEY FK_7D01E7A2639666D6');
        $this->addSql('ALTER TABLE menu_day ADD CONSTRAINT FK_7D01E7A2639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_day DROP FOREIGN KEY FK_7D01E7A2639666D6');
        $this->addSql('ALTER TABLE menu_day ADD CONSTRAINT FK_7D01E7A2639666D6 FOREIGN KEY (meal_id) REFERENCES meal (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
