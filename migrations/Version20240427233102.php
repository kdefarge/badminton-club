<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240427233102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE encounter_set_result_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE score_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE score (id INT NOT NULL, encounter_id INT NOT NULL, number SMALLINT NOT NULL, score_team1 SMALLINT NOT NULL, score_team2 SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_32993751D6E2FADC ON score (encounter_id)');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_32993751D6E2FADC FOREIGN KEY (encounter_id) REFERENCES encounter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE encounter_set_result DROP CONSTRAINT fk_d17f8a3bd6e2fadc');
        $this->addSql('DROP TABLE encounter_set_result');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE score_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE encounter_set_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE encounter_set_result (id INT NOT NULL, encounter_id INT NOT NULL, number SMALLINT NOT NULL, score_team1 SMALLINT NOT NULL, score_team2 SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_d17f8a3bd6e2fadc ON encounter_set_result (encounter_id)');
        $this->addSql('ALTER TABLE encounter_set_result ADD CONSTRAINT fk_d17f8a3bd6e2fadc FOREIGN KEY (encounter_id) REFERENCES encounter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE score DROP CONSTRAINT FK_32993751D6E2FADC');
        $this->addSql('DROP TABLE score');
    }
}
