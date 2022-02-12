<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\RoomStatus;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220210011426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE room_status_id_seq START 1');

        $statuses = [
            ['code' => RoomStatus::NEW_STATUS, 'title' => 'Игра в ожидании игроков'],
            ['code' => RoomStatus::RUNNING_STATUS, 'title' => 'Начало игры'],
            ['code' => RoomStatus::ACTION_TIME_STATUS, 'title' => 'Время для выбора карточки'],
            ['code' => RoomStatus::EVALUATE_TIME_STATUS, 'title' => 'Время для оценки участников'],
            ['code' => RoomStatus::THROWN_STATUS, 'title' => 'Игра была брошена'],
            ['code' => RoomStatus::CLOSE_STATUS, 'title' => 'Игра была закрыта'],
        ];

        foreach ($statuses as $status) {
            $this->addSql(
                'INSERT INTO room_status("id", "title", "code") VALUES(nextval(\'room_status_id_seq\'), :title , :code)',
                $status
            );
        }
    }

    public function down(Schema $schema): void
    {
    }
}
