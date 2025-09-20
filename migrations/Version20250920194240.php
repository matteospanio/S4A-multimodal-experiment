<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920194240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add title and description fields to experiment table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE experiment ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE experiment ADD description TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE experiment DROP title');
        $this->addSql('ALTER TABLE experiment DROP description');
    }
}
