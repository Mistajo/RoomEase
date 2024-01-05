<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105171348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, meetingroom_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, start_date DATETIME DEFAULT NULL, end_date DATETIME DEFAULT NULL, statut VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_42C84955A76ED395 (user_id), INDEX IDX_42C84955AD4CA652 (meetingroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE search_meeting_room (search_id INT NOT NULL, meeting_room_id INT NOT NULL, INDEX IDX_89A27E97650760A9 (search_id), INDEX IDX_89A27E97CCC5381E (meeting_room_id), PRIMARY KEY(search_id, meeting_room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955AD4CA652 FOREIGN KEY (meetingroom_id) REFERENCES meeting_room (id)');
        $this->addSql('ALTER TABLE search_meeting_room ADD CONSTRAINT FK_89A27E97650760A9 FOREIGN KEY (search_id) REFERENCES search (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE search_meeting_room ADD CONSTRAINT FK_89A27E97CCC5381E FOREIGN KEY (meeting_room_id) REFERENCES meeting_room (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955AD4CA652');
        $this->addSql('ALTER TABLE search_meeting_room DROP FOREIGN KEY FK_89A27E97650760A9');
        $this->addSql('ALTER TABLE search_meeting_room DROP FOREIGN KEY FK_89A27E97CCC5381E');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE search_meeting_room');
    }
}
