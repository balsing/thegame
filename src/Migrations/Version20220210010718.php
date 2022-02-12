<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210010718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card (id INT NOT NULL, title VARCHAR(255) DEFAULT NULL, file VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE question (id INT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE room (id INT NOT NULL, status_id INT NOT NULL, owner_id INT NOT NULL, last_stage_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_729F519B6BF700BD ON room (status_id)');
        $this->addSql('CREATE INDEX IDX_729F519B7E3C61F9 ON room (owner_id)');
        $this->addSql('CREATE INDEX IDX_729F519B5AF9940D ON room (last_stage_id)');
        $this->addSql('COMMENT ON COLUMN room.id IS \'Идентификатор\'');
        $this->addSql('COMMENT ON COLUMN room.status_id IS \'Идентификатор\'');
        $this->addSql('COMMENT ON COLUMN room.owner_id IS \'Идентификатор пользователя\'');
        $this->addSql('COMMENT ON COLUMN room.code IS \'Символьный код комнаты\'');
        $this->addSql('CREATE TABLE room_status (id INT NOT NULL, code VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN room_status.id IS \'Идентификатор\'');
        $this->addSql('COMMENT ON COLUMN room_status.code IS \'Символьный код статуса\'');
        $this->addSql('COMMENT ON COLUMN room_status.title IS \'Название статуса\'');
        $this->addSql('CREATE TABLE stage (id INT NOT NULL, room_id INT NOT NULL, question_id INT NOT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C27C936954177093 ON stage (room_id)');
        $this->addSql('CREATE INDEX IDX_C27C93691E27F6BF ON stage (question_id)');
        $this->addSql('COMMENT ON COLUMN stage.room_id IS \'Идентификатор\'');
        $this->addSql('CREATE TABLE stage_result (id INT NOT NULL, stage_id INT NOT NULL, player_id INT NOT NULL, card_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8FE360082298D193 ON stage_result (stage_id)');
        $this->addSql('CREATE INDEX IDX_8FE3600899E6F5DF ON stage_result (player_id)');
        $this->addSql('CREATE INDEX IDX_8FE360084ACC9A20 ON stage_result (card_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nickname VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'Идентификатор пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.username IS \'Никнейм пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'Роли пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.password IS \'Пароль пользователя\'');
        $this->addSql('CREATE TABLE users_to_room (id INT NOT NULL, player_id INT NOT NULL, room_id INT NOT NULL, is_owner BOOLEAN NOT NULL, score INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6571349E99E6F5DF ON users_to_room (player_id)');
        $this->addSql('CREATE INDEX IDX_6571349E54177093 ON users_to_room (room_id)');
        $this->addSql('COMMENT ON COLUMN users_to_room.player_id IS \'Идентификатор пользователя\'');
        $this->addSql('COMMENT ON COLUMN users_to_room.room_id IS \'Идентификатор\'');
        $this->addSql('CREATE TABLE users_to_room_card (users_to_room_id INT NOT NULL, card_id INT NOT NULL, PRIMARY KEY(users_to_room_id, card_id))');
        $this->addSql('CREATE INDEX IDX_17F783CADE4E2F2 ON users_to_room_card (users_to_room_id)');
        $this->addSql('CREATE INDEX IDX_17F783CA4ACC9A20 ON users_to_room_card (card_id)');
        $this->addSql('CREATE IF NOT EXISTS TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B6BF700BD FOREIGN KEY (status_id) REFERENCES room_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B5AF9940D FOREIGN KEY (last_stage_id) REFERENCES stage (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stage ADD CONSTRAINT FK_C27C936954177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stage ADD CONSTRAINT FK_C27C93691E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stage_result ADD CONSTRAINT FK_8FE360082298D193 FOREIGN KEY (stage_id) REFERENCES stage (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stage_result ADD CONSTRAINT FK_8FE3600899E6F5DF FOREIGN KEY (player_id) REFERENCES users_to_room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stage_result ADD CONSTRAINT FK_8FE360084ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_to_room ADD CONSTRAINT FK_6571349E99E6F5DF FOREIGN KEY (player_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_to_room ADD CONSTRAINT FK_6571349E54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_to_room_card ADD CONSTRAINT FK_17F783CADE4E2F2 FOREIGN KEY (users_to_room_id) REFERENCES users_to_room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_to_room_card ADD CONSTRAINT FK_17F783CA4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stage_result DROP CONSTRAINT FK_8FE360084ACC9A20');
        $this->addSql('ALTER TABLE users_to_room_card DROP CONSTRAINT FK_17F783CA4ACC9A20');
        $this->addSql('ALTER TABLE stage DROP CONSTRAINT FK_C27C93691E27F6BF');
        $this->addSql('ALTER TABLE stage DROP CONSTRAINT FK_C27C936954177093');
        $this->addSql('ALTER TABLE users_to_room DROP CONSTRAINT FK_6571349E54177093');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519B6BF700BD');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519B5AF9940D');
        $this->addSql('ALTER TABLE stage_result DROP CONSTRAINT FK_8FE360082298D193');
        $this->addSql('ALTER TABLE room DROP CONSTRAINT FK_729F519B7E3C61F9');
        $this->addSql('ALTER TABLE users_to_room DROP CONSTRAINT FK_6571349E99E6F5DF');
        $this->addSql('ALTER TABLE stage_result DROP CONSTRAINT FK_8FE3600899E6F5DF');
        $this->addSql('ALTER TABLE users_to_room_card DROP CONSTRAINT FK_17F783CADE4E2F2');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_status');
        $this->addSql('DROP TABLE stage');
        $this->addSql('DROP TABLE stage_result');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_to_room');
        $this->addSql('DROP TABLE users_to_room_card');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
