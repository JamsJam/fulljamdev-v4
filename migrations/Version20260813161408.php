<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260813161408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appointment (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, edited_at DATETIME NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, title VARCHAR(70) NOT NULL, description LONGTEXT DEFAULT NULL, transcription LONGTEXT DEFAULT NULL, link VARCHAR(255) NOT NULL, planning_id INT NOT NULL, INDEX IDX_FE38F8443D865311 (planning_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, dow SMALLINT NOT NULL, start_hour TIME NOT NULL, end_hour TIME NOT NULL, planning_id INT NOT NULL, INDEX IDX_3FB7A2BF3D865311 (planning_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE planning (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(70) NOT NULL, description LONGTEXT DEFAULT NULL, duration SMALLINT NOT NULL, gap SMALLINT NOT NULL, color VARCHAR(7) NOT NULL, created_at DATETIME NOT NULL, edited_at DATETIME NOT NULL, is_active TINYINT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_D499BFF6665648E9 (color), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE unavailability (id INT AUTO_INCREMENT NOT NULL, start_at DATETIME NOT NULL, end_at DATETIME NOT NULL, raison VARCHAR(100) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(50) NOT NULL, formname VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F8443D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BF3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F8443D865311');
        $this->addSql('ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BF3D865311');
        $this->addSql('DROP TABLE appointment');
        $this->addSql('DROP TABLE availability');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE unavailability');
        $this->addSql('DROP TABLE user');
    }
}
