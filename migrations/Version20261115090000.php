<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261115090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la destination de redirection du QR code de la carte de visite.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE qr_visit_card (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(2048) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE qr_visit_card');
    }
}
