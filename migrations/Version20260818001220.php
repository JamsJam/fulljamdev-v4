<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260818001220 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des contacts et association obligatoire des rendez-vous existants';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, phone_number VARCHAR(30) NOT NULL, company VARCHAR(150) DEFAULT NULL, job_title VARCHAR(150) DEFAULT NULL, source VARCHAR(100) DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE appointment ADD contact_id INT DEFAULT NULL');
        $this->addSql("INSERT INTO contact (id, first_name, last_name, email, phone_number, source, created_at, updated_at) SELECT id, 'Contact', 'importé', CONCAT('appointment-', id, '@legacy.local'), 'Non renseigné', 'migration', created_at, edited_at FROM appointment");
        $this->addSql('UPDATE appointment SET contact_id = id WHERE contact_id IS NULL');
        $this->addSql('ALTER TABLE appointment MODIFY contact_id INT NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id)');
        $this->addSql('CREATE INDEX IDX_FE38F844E7A1254A ON appointment (contact_id)');
        $this->addSql('ALTER TABLE appointment MODIFY link VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844E7A1254A');
        $this->addSql('DROP INDEX IDX_FE38F844E7A1254A ON appointment');
        $this->addSql('ALTER TABLE appointment DROP contact_id');
        $this->addSql("UPDATE appointment SET link = '' WHERE link IS NULL");
        $this->addSql('ALTER TABLE appointment MODIFY link VARCHAR(255) NOT NULL');
        $this->addSql('DROP TABLE contact');
    }
}
