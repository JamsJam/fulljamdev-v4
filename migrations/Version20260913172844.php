<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260913172844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute un identifiant d’ancrage facultatif aux blocs de page.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_page_block ADD anchor_id VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE project_media ADD path VARCHAR(255) NOT NULL, ADD mime_type VARCHAR(127) NOT NULL, ADD size INT NOT NULL, ADD created_at DATETIME NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7979A892B548B0F ON project_media (path)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_page_block DROP anchor_id');
        $this->addSql('DROP INDEX UNIQ_7979A892B548B0F ON project_media');
        $this->addSql('ALTER TABLE project_media DROP path, DROP mime_type, DROP size, DROP created_at');
    }
}
