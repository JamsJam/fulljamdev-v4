<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260830165534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Intègre toutes les informations du compte administrateur dans la table user.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD last_name VARCHAR(100) NOT NULL,
            ADD first_name VARCHAR(100) NOT NULL,
            ADD phone_number VARCHAR(30) NOT NULL,
            ADD company VARCHAR(150) NOT NULL,
            ADD job_title VARCHAR(150) NOT NULL,
            DROP nom,
            DROP formname'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(50) NOT NULL, ADD formname VARCHAR(50) NOT NULL, DROP last_name, DROP first_name, DROP phone_number, DROP company, DROP job_title');
    }
}
