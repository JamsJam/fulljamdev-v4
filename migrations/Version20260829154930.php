<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260829154930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_article ADD summary VARCHAR(160) DEFAULT NULL, ADD seo_title VARCHAR(60) DEFAULT NULL, ADD seo_description VARCHAR(160) DEFAULT NULL, ADD archived_at DATETIME DEFAULT NULL, DROP excerpt, CHANGE title title VARCHAR(50) DEFAULT NULL, CHANGE slug slug VARCHAR(200) DEFAULT NULL, CHANGE content content LONGTEXT DEFAULT NULL, CHANGE status status VARCHAR(20) DEFAULT \'draft\' NOT NULL, CHANGE featured_image cover_image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_article ADD excerpt VARCHAR(320) DEFAULT NULL, DROP summary, DROP seo_title, DROP seo_description, DROP archived_at, CHANGE title title VARCHAR(180) NOT NULL, CHANGE slug slug VARCHAR(200) NOT NULL, CHANGE content content LONGTEXT NOT NULL, CHANGE status status VARCHAR(30) DEFAULT \'draft\' NOT NULL, CHANGE cover_image featured_image VARCHAR(255) DEFAULT NULL');
    }
}
