<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260828205743 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les expériences, le blog et les projets, puis importe la chronologie historique';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(200) NOT NULL, excerpt VARCHAR(320) DEFAULT NULL, content LONGTEXT NOT NULL, featured_image VARCHAR(255) DEFAULT NULL, status VARCHAR(30) DEFAULT \'draft\' NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, category_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_DD32D7D5989D9B62 (slug), INDEX IDX_DD32D7D512469DE2 (category_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE content_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, slug VARCHAR(140) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_54FBF32E989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE content_experience (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(80) NOT NULL, title VARCHAR(180) NOT NULL, company VARCHAR(160) NOT NULL, contract_type VARCHAR(255) DEFAULT NULL, begin_at DATE NOT NULL, end_at DATE DEFAULT NULL, about JSON NOT NULL, is_visible TINYINT DEFAULT 1 NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE content_project (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(180) NOT NULL, slug VARCHAR(200) NOT NULL, excerpt VARCHAR(320) DEFAULT NULL, content LONGTEXT NOT NULL, featured_image VARCHAR(255) DEFAULT NULL, technologies JSON NOT NULL, website_url VARCHAR(255) DEFAULT NULL, repository_url VARCHAR(255) DEFAULT NULL, is_featured TINYINT DEFAULT 0 NOT NULL, status VARCHAR(30) DEFAULT \'draft\' NOT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_F0BB095D989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE content_article ADD CONSTRAINT FK_DD32D7D512469DE2 FOREIGN KEY (category_id) REFERENCES content_category (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_article DROP FOREIGN KEY FK_DD32D7D512469DE2');
        $this->addSql('DROP TABLE content_article');
        $this->addSql('DROP TABLE content_category');
        $this->addSql('DROP TABLE content_experience');
        $this->addSql('DROP TABLE content_project');
    }
}
