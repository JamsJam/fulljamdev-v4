<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261116160659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute le profil LinkTree et sa collection de liens.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE link_tree (id INT AUTO_INCREMENT NOT NULL, profile_picture VARCHAR(2048) DEFAULT NULL, name VARCHAR(180) DEFAULT NULL, company VARCHAR(180) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE link_tree_link (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(180) DEFAULT NULL, href VARCHAR(2048) DEFAULT NULL, is_online TINYINT DEFAULT 0 NOT NULL, is_prefered TINYINT DEFAULT 0 NOT NULL, link_tree_id INT NOT NULL, INDEX IDX_6B5A23B27AED3E57 (link_tree_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE link_tree_link ADD CONSTRAINT FK_6B5A23B27AED3E57 FOREIGN KEY (link_tree_id) REFERENCES link_tree (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE link_tree_link');
        $this->addSql('DROP TABLE link_tree');
    }
}
