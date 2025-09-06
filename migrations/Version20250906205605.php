<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250906205605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, fistname VARCHAR(255) NOT NULL, description VARCHAR(50) NOT NULL, link VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE blog_author_post (blog_author_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_675578AC530B1B54 (blog_author_id), INDEX IDX_675578AC4B89032C (post_id), PRIMARY KEY (blog_author_id, post_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE blog_author_post ADD CONSTRAINT FK_675578AC530B1B54 FOREIGN KEY (blog_author_id) REFERENCES blog_author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_author_post ADD CONSTRAINT FK_675578AC4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES blog_author (id)');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_author_post DROP FOREIGN KEY FK_675578AC530B1B54');
        $this->addSql('ALTER TABLE blog_author_post DROP FOREIGN KEY FK_675578AC4B89032C');
        $this->addSql('DROP TABLE blog_author');
        $this->addSql('DROP TABLE blog_author_post');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DF675F31B');
        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B ON post');
        $this->addSql('ALTER TABLE post DROP author_id');
    }
}
