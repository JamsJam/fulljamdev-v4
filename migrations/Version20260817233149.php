<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260817233149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE summary (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, internal_notes LONGTEXT DEFAULT NULL, transcription LONGTEXT DEFAULT NULL, recording_link VARCHAR(2048) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, appointment_id INT NOT NULL, UNIQUE INDEX UNIQ_CE286663E5B533F9 (appointment_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE summary ADD CONSTRAINT FK_CE286663E5B533F9 FOREIGN KEY (appointment_id) REFERENCES appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment ADD status VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE summary DROP FOREIGN KEY FK_CE286663E5B533F9');
        $this->addSql('DROP TABLE summary');
        $this->addSql('ALTER TABLE appointment DROP status');
    }
}
