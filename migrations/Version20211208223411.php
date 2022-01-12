<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211208223411 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping ADD freecategory_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE shopping ADD CONSTRAINT FK_FB45F43966FABB45 FOREIGN KEY (freecategory_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_FB45F43966FABB45 ON shopping (freecategory_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping DROP FOREIGN KEY FK_FB45F43966FABB45');
        $this->addSql('DROP INDEX IDX_FB45F43966FABB45 ON shopping');
        $this->addSql('ALTER TABLE shopping DROP freecategory_id');
    }
}
