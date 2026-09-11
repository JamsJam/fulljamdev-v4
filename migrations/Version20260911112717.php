<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260911112717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les métadonnées des médias des articles de blog.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_article_media (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(16) NOT NULL, technical_name VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, mime_type VARCHAR(127) NOT NULL, size INT NOT NULL, created_at DATETIME NOT NULL, article_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_4641F50C72263045 (technical_name), UNIQUE INDEX UNIQ_4641F50CB548B0F (path), INDEX IDX_4641F50C7294869C (article_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE content_article_media ADD CONSTRAINT FK_4641F50C7294869C FOREIGN KEY (article_id) REFERENCES content_article (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_article_media DROP FOREIGN KEY FK_4641F50C7294869C');
        $this->addSql('DROP TABLE content_article_media');
    }
}
