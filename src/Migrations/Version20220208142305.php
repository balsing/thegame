<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208142305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE users_to_room_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users_to_room (id INT NOT NULL, player_id INT NOT NULL, room_id INT NOT NULL, is_owner BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6571349E99E6F5DF ON users_to_room (player_id)');
        $this->addSql('CREATE INDEX IDX_6571349E54177093 ON users_to_room (room_id)');
        $this->addSql('COMMENT ON COLUMN users_to_room.player_id IS \'Идентификатор пользователя\'');
        $this->addSql('COMMENT ON COLUMN users_to_room.room_id IS \'Идентификатор\'');
        $this->addSql('ALTER TABLE users_to_room ADD CONSTRAINT FK_6571349E99E6F5DF FOREIGN KEY (player_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_to_room ADD CONSTRAINT FK_6571349E54177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE users_to_room_id_seq CASCADE');
        $this->addSql('DROP TABLE users_to_room');
    }
}
