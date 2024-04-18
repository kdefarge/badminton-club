<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240418150819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE encounter_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE encounter_player_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE encounter_set_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE gender_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE skill_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tournament_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE encounter (id INT NOT NULL, tournament_id INT DEFAULT NULL, is_finished BOOLEAN NOT NULL, is_team1_won BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_69D229CA33D1A3E7 ON encounter (tournament_id)');
        $this->addSql('COMMENT ON COLUMN encounter.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN encounter.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE encounter_player (id INT NOT NULL, encounter_id INT NOT NULL, is_team1 BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9CFF9D6DD6E2FADC ON encounter_player (encounter_id)');
        $this->addSql('CREATE TABLE encounter_player_player (encounter_player_id INT NOT NULL, player_id INT NOT NULL, PRIMARY KEY(encounter_player_id, player_id))');
        $this->addSql('CREATE INDEX IDX_17412CCECFA38605 ON encounter_player_player (encounter_player_id)');
        $this->addSql('CREATE INDEX IDX_17412CCE99E6F5DF ON encounter_player_player (player_id)');
        $this->addSql('CREATE TABLE encounter_set_result (id INT NOT NULL, encounter_id INT NOT NULL, number SMALLINT NOT NULL, score_team1 SMALLINT NOT NULL, score_team2 SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D17F8A3BD6E2FADC ON encounter_set_result (encounter_id)');
        $this->addSql('CREATE TABLE gender (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE player (id INT NOT NULL, gender_id INT DEFAULT NULL, skill_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98197A65708A0E0 ON player (gender_id)');
        $this->addSql('CREATE INDEX IDX_98197A655585C142 ON player (skill_id)');
        $this->addSql('CREATE TABLE skill (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tournament (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tournament.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tournament.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE tournament_player (tournament_id INT NOT NULL, player_id INT NOT NULL, PRIMARY KEY(tournament_id, player_id))');
        $this->addSql('CREATE INDEX IDX_FCB3843533D1A3E7 ON tournament_player (tournament_id)');
        $this->addSql('CREATE INDEX IDX_FCB3843599E6F5DF ON tournament_player (player_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE encounter ADD CONSTRAINT FK_69D229CA33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE encounter_player ADD CONSTRAINT FK_9CFF9D6DD6E2FADC FOREIGN KEY (encounter_id) REFERENCES encounter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE encounter_player_player ADD CONSTRAINT FK_17412CCECFA38605 FOREIGN KEY (encounter_player_id) REFERENCES encounter_player (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE encounter_player_player ADD CONSTRAINT FK_17412CCE99E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE encounter_set_result ADD CONSTRAINT FK_D17F8A3BD6E2FADC FOREIGN KEY (encounter_id) REFERENCES encounter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A655585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_player ADD CONSTRAINT FK_FCB3843533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES tournament (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tournament_player ADD CONSTRAINT FK_FCB3843599E6F5DF FOREIGN KEY (player_id) REFERENCES player (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE encounter_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE encounter_player_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE encounter_set_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE gender_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE skill_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tournament_id_seq CASCADE');
        $this->addSql('ALTER TABLE encounter DROP CONSTRAINT FK_69D229CA33D1A3E7');
        $this->addSql('ALTER TABLE encounter_player DROP CONSTRAINT FK_9CFF9D6DD6E2FADC');
        $this->addSql('ALTER TABLE encounter_player_player DROP CONSTRAINT FK_17412CCECFA38605');
        $this->addSql('ALTER TABLE encounter_player_player DROP CONSTRAINT FK_17412CCE99E6F5DF');
        $this->addSql('ALTER TABLE encounter_set_result DROP CONSTRAINT FK_D17F8A3BD6E2FADC');
        $this->addSql('ALTER TABLE player DROP CONSTRAINT FK_98197A65708A0E0');
        $this->addSql('ALTER TABLE player DROP CONSTRAINT FK_98197A655585C142');
        $this->addSql('ALTER TABLE tournament_player DROP CONSTRAINT FK_FCB3843533D1A3E7');
        $this->addSql('ALTER TABLE tournament_player DROP CONSTRAINT FK_FCB3843599E6F5DF');
        $this->addSql('DROP TABLE encounter');
        $this->addSql('DROP TABLE encounter_player');
        $this->addSql('DROP TABLE encounter_player_player');
        $this->addSql('DROP TABLE encounter_set_result');
        $this->addSql('DROP TABLE gender');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE tournament');
        $this->addSql('DROP TABLE tournament_player');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
