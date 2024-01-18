<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116152010 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statistic_meeting_room (statistic_id INT NOT NULL, meeting_room_id INT NOT NULL, INDEX IDX_B78631DC53B6268F (statistic_id), INDEX IDX_B78631DCCCC5381E (meeting_room_id), PRIMARY KEY(statistic_id, meeting_room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statistic_meeting_room ADD CONSTRAINT FK_B78631DC53B6268F FOREIGN KEY (statistic_id) REFERENCES statistic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE statistic_meeting_room ADD CONSTRAINT FK_B78631DCCCC5381E FOREIGN KEY (meeting_room_id) REFERENCES meeting_room (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statistic_meeting_room DROP FOREIGN KEY FK_B78631DC53B6268F');
        $this->addSql('ALTER TABLE statistic_meeting_room DROP FOREIGN KEY FK_B78631DCCCC5381E');
        $this->addSql('DROP TABLE statistic_meeting_room');
    }
}
