<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109141301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE meeting_room_equipment (meeting_room_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_CE9AD3ABCCC5381E (meeting_room_id), INDEX IDX_CE9AD3AB517FE9FE (equipment_id), PRIMARY KEY(meeting_room_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE meeting_room_equipment ADD CONSTRAINT FK_CE9AD3ABCCC5381E FOREIGN KEY (meeting_room_id) REFERENCES meeting_room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meeting_room_equipment ADD CONSTRAINT FK_CE9AD3AB517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE meeting_room_equipment DROP FOREIGN KEY FK_CE9AD3ABCCC5381E');
        $this->addSql('ALTER TABLE meeting_room_equipment DROP FOREIGN KEY FK_CE9AD3AB517FE9FE');
        $this->addSql('DROP TABLE meeting_room_equipment');
    }
}
