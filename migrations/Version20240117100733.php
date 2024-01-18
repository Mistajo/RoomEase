<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240117100733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statistic ADD meetingroom_id INT DEFAULT NULL, ADD reservation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE statistic ADD CONSTRAINT FK_649B469CAD4CA652 FOREIGN KEY (meetingroom_id) REFERENCES meeting_room (id)');
        $this->addSql('ALTER TABLE statistic ADD CONSTRAINT FK_649B469CB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE INDEX IDX_649B469CAD4CA652 ON statistic (meetingroom_id)');
        $this->addSql('CREATE INDEX IDX_649B469CB83297E7 ON statistic (reservation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statistic DROP FOREIGN KEY FK_649B469CAD4CA652');
        $this->addSql('ALTER TABLE statistic DROP FOREIGN KEY FK_649B469CB83297E7');
        $this->addSql('DROP INDEX IDX_649B469CAD4CA652 ON statistic');
        $this->addSql('DROP INDEX IDX_649B469CB83297E7 ON statistic');
        $this->addSql('ALTER TABLE statistic DROP meetingroom_id, DROP reservation_id');
    }
}
