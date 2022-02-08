<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220208115530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'Идентификатор пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.username IS \'Никнейм пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.roles IS \'Роли пользователя\'');
        $this->addSql('COMMENT ON COLUMN users.password IS \'Пароль пользователя\'');
        $this->addSql('ALTER TABLE room ADD questions TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD cards TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN room.questions IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN room.cards IS \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE room DROP questions');
        $this->addSql('ALTER TABLE room DROP cards');
    }
}
