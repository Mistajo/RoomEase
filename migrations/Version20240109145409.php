<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109145409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE search_equipment (search_id INT NOT NULL, equipment_id INT NOT NULL, INDEX IDX_6823C52B650760A9 (search_id), INDEX IDX_6823C52B517FE9FE (equipment_id), PRIMARY KEY(search_id, equipment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE search_equipment ADD CONSTRAINT FK_6823C52B650760A9 FOREIGN KEY (search_id) REFERENCES search (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE search_equipment ADD CONSTRAINT FK_6823C52B517FE9FE FOREIGN KEY (equipment_id) REFERENCES equipment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE search ADD name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE search_equipment DROP FOREIGN KEY FK_6823C52B650760A9');
        $this->addSql('ALTER TABLE search_equipment DROP FOREIGN KEY FK_6823C52B517FE9FE');
        $this->addSql('DROP TABLE search_equipment');
        $this->addSql('ALTER TABLE search DROP name');
    }
}
