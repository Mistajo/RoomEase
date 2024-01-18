<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240116175129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statistic_reservation (statistic_id INT NOT NULL, reservation_id INT NOT NULL, INDEX IDX_7399442C53B6268F (statistic_id), INDEX IDX_7399442CB83297E7 (reservation_id), PRIMARY KEY(statistic_id, reservation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE statistic_reservation ADD CONSTRAINT FK_7399442C53B6268F FOREIGN KEY (statistic_id) REFERENCES statistic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE statistic_reservation ADD CONSTRAINT FK_7399442CB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE statistic_reservation DROP FOREIGN KEY FK_7399442C53B6268F');
        $this->addSql('ALTER TABLE statistic_reservation DROP FOREIGN KEY FK_7399442CB83297E7');
        $this->addSql('DROP TABLE statistic_reservation');
    }
}
