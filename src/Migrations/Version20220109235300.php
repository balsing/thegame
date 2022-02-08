<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220109235300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE room_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE room (id INT NOT NULL, status_id INT NOT NULL, code VARCHAR(255) NOT NULL, players JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_729F519B6BF700BD ON room (status_id)');
        $this->addSql('COMMENT ON COLUMN room.id IS \'Идентификатор\'');
        $this->addSql('COMMENT ON COLUMN room.status_id IS \'Идентификатор\'');
        $this->addSql('COMMENT ON COLUMN room.code IS \'Символьный код комнаты\'');
        $this->addSql('COMMENT ON COLUMN room.players IS \'Игроки и их счёт\'');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B6BF700BD FOREIGN KEY (status_id) REFERENCES room_status (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE room_id_seq CASCADE');
        $this->addSql('DROP TABLE room');
    }
}
