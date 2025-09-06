<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250906194248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(45) NOT NULL, created_at DATETIME NOT NULL, edited_at DATETIME NOT NULL, description VARCHAR(160) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_tags_post (blog_tags_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_F0532892618DF237 (blog_tags_id), INDEX IDX_F05328924B89032C (post_id), PRIMARY KEY (blog_tags_id, post_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE blog_tags_post ADD CONSTRAINT FK_F0532892618DF237 FOREIGN KEY (blog_tags_id) REFERENCES blog_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_tags_post ADD CONSTRAINT FK_F05328924B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_tags_post DROP FOREIGN KEY FK_F0532892618DF237');
        $this->addSql('ALTER TABLE blog_tags_post DROP FOREIGN KEY FK_F05328924B89032C');
        $this->addSql('DROP TABLE blog_tags');
        $this->addSql('DROP TABLE blog_tags_post');
    }
}
