<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250906213333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_author ADD created_at DATETIME NOT NULL, ADD edited_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE post CHANGE title title VARCHAR(56) DEFAULT NULL, CHANGE description description VARCHAR(160) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_author DROP created_at, DROP edited_at');
        $this->addSql('ALTER TABLE post CHANGE title title VARCHAR(56) NOT NULL, CHANGE description description VARCHAR(160) NOT NULL');
    }
}
