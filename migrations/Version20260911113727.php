<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260911113727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_media (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(16) NOT NULL, technical_name VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, project_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_7979A89272263045 (technical_name), INDEX IDX_7979A892166D1F9C (project_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE project_media ADD CONSTRAINT FK_7979A892166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_media DROP FOREIGN KEY FK_7979A892166D1F9C');
        $this->addSql('DROP TABLE project_media');
    }
}
