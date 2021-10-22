<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018093624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE week (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, INDEX IDX_5B5A69C0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE week_day (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', week_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', day INT NOT NULL, time INT NOT NULL, preparations LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_256D1361C86F3B2F (week_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE week_day_theme (week_day_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', theme_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_E6F91EC7DB83875 (week_day_id), INDEX IDX_E6F91EC59027487 (theme_id), PRIMARY KEY(week_day_id, theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE week ADD CONSTRAINT FK_5B5A69C0A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE week_day ADD CONSTRAINT FK_256D1361C86F3B2F FOREIGN KEY (week_id) REFERENCES week (id)');
        $this->addSql('ALTER TABLE week_day_theme ADD CONSTRAINT FK_E6F91EC7DB83875 FOREIGN KEY (week_day_id) REFERENCES week_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE week_day_theme ADD CONSTRAINT FK_E6F91EC59027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE week_day DROP FOREIGN KEY FK_256D1361C86F3B2F');
        $this->addSql('ALTER TABLE week_day_theme DROP FOREIGN KEY FK_E6F91EC7DB83875');
        $this->addSql('DROP TABLE week');
        $this->addSql('DROP TABLE week_day');
        $this->addSql('DROP TABLE week_day_theme');
    }
}
