<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250920083325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE experiment (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE flavor_to_music_trial (id INT NOT NULL, song_id INT NOT NULL, choice_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_88EBE066A0BDB2F3 ON flavor_to_music_trial (song_id)');
        $this->addSql('CREATE INDEX IDX_88EBE066998666D1 ON flavor_to_music_trial (choice_id)');
        $this->addSql('CREATE TABLE flavor_to_music_trial_flavor (flavor_to_music_trial_id INT NOT NULL, flavor_id INT NOT NULL, PRIMARY KEY(flavor_to_music_trial_id, flavor_id))');
        $this->addSql('CREATE INDEX IDX_CCDDD99043BDBA36 ON flavor_to_music_trial_flavor (flavor_to_music_trial_id)');
        $this->addSql('CREATE INDEX IDX_CCDDD990FDDA6450 ON flavor_to_music_trial_flavor (flavor_id)');
        $this->addSql('CREATE TABLE music_to_flavor_trial (id INT NOT NULL, flavor_id INT NOT NULL, choice_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A9E2E267FDDA6450 ON music_to_flavor_trial (flavor_id)');
        $this->addSql('CREATE INDEX IDX_A9E2E267998666D1 ON music_to_flavor_trial (choice_id)');
        $this->addSql('CREATE TABLE music_to_flavor_trial_song (music_to_flavor_trial_id INT NOT NULL, song_id INT NOT NULL, PRIMARY KEY(music_to_flavor_trial_id, song_id))');
        $this->addSql('CREATE INDEX IDX_EC0FBF3A9950A06 ON music_to_flavor_trial_song (music_to_flavor_trial_id)');
        $this->addSql('CREATE INDEX IDX_EC0FBF3AA0BDB2F3 ON music_to_flavor_trial_song (song_id)');
        $this->addSql('CREATE TABLE task (id SERIAL NOT NULL, experiment_id INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB25FF444C8 ON task (experiment_id)');
        $this->addSql('ALTER TABLE flavor_to_music_trial ADD CONSTRAINT FK_88EBE066A0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flavor_to_music_trial ADD CONSTRAINT FK_88EBE066998666D1 FOREIGN KEY (choice_id) REFERENCES flavor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flavor_to_music_trial ADD CONSTRAINT FK_88EBE066BF396750 FOREIGN KEY (id) REFERENCES trial (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flavor_to_music_trial_flavor ADD CONSTRAINT FK_CCDDD99043BDBA36 FOREIGN KEY (flavor_to_music_trial_id) REFERENCES flavor_to_music_trial (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE flavor_to_music_trial_flavor ADD CONSTRAINT FK_CCDDD990FDDA6450 FOREIGN KEY (flavor_id) REFERENCES flavor (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE music_to_flavor_trial ADD CONSTRAINT FK_A9E2E267FDDA6450 FOREIGN KEY (flavor_id) REFERENCES flavor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE music_to_flavor_trial ADD CONSTRAINT FK_A9E2E267998666D1 FOREIGN KEY (choice_id) REFERENCES song (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE music_to_flavor_trial ADD CONSTRAINT FK_A9E2E267BF396750 FOREIGN KEY (id) REFERENCES trial (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE music_to_flavor_trial_song ADD CONSTRAINT FK_EC0FBF3A9950A06 FOREIGN KEY (music_to_flavor_trial_id) REFERENCES music_to_flavor_trial (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE music_to_flavor_trial_song ADD CONSTRAINT FK_EC0FBF3AA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25FF444C8 FOREIGN KEY (experiment_id) REFERENCES experiment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE song DROP expected_flavor');
        $this->addSql('ALTER TABLE trial ADD task_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trial RENAME COLUMN task_type TO type');
        $this->addSql('ALTER TABLE trial ADD CONSTRAINT FK_74A25E3F8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_74A25E3F8DB60186 ON trial (task_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trial DROP CONSTRAINT FK_74A25E3F8DB60186');
        $this->addSql('ALTER TABLE flavor_to_music_trial DROP CONSTRAINT FK_88EBE066A0BDB2F3');
        $this->addSql('ALTER TABLE flavor_to_music_trial DROP CONSTRAINT FK_88EBE066998666D1');
        $this->addSql('ALTER TABLE flavor_to_music_trial DROP CONSTRAINT FK_88EBE066BF396750');
        $this->addSql('ALTER TABLE flavor_to_music_trial_flavor DROP CONSTRAINT FK_CCDDD99043BDBA36');
        $this->addSql('ALTER TABLE flavor_to_music_trial_flavor DROP CONSTRAINT FK_CCDDD990FDDA6450');
        $this->addSql('ALTER TABLE music_to_flavor_trial DROP CONSTRAINT FK_A9E2E267FDDA6450');
        $this->addSql('ALTER TABLE music_to_flavor_trial DROP CONSTRAINT FK_A9E2E267998666D1');
        $this->addSql('ALTER TABLE music_to_flavor_trial DROP CONSTRAINT FK_A9E2E267BF396750');
        $this->addSql('ALTER TABLE music_to_flavor_trial_song DROP CONSTRAINT FK_EC0FBF3A9950A06');
        $this->addSql('ALTER TABLE music_to_flavor_trial_song DROP CONSTRAINT FK_EC0FBF3AA0BDB2F3');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25FF444C8');
        $this->addSql('DROP TABLE experiment');
        $this->addSql('DROP TABLE flavor_to_music_trial');
        $this->addSql('DROP TABLE flavor_to_music_trial_flavor');
        $this->addSql('DROP TABLE music_to_flavor_trial');
        $this->addSql('DROP TABLE music_to_flavor_trial_song');
        $this->addSql('DROP TABLE task');
        $this->addSql('ALTER TABLE song ADD expected_flavor TEXT NOT NULL');
        $this->addSql('COMMENT ON COLUMN song.expected_flavor IS \'(DC2Type:array)\'');
        $this->addSql('DROP INDEX IDX_74A25E3F8DB60186');
        $this->addSql('ALTER TABLE trial DROP task_id');
        $this->addSql('ALTER TABLE trial RENAME COLUMN type TO task_type');
    }
}
