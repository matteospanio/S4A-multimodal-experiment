<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917200516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove Participant entity and its associated table and sequence';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE participant_id_seq CASCADE');
        $this->addSql('DROP TABLE participant');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE participant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE participant (id SERIAL NOT NULL, name VARCHAR(255) DEFAULT NULL, age VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
    }
}
