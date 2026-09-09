<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260830015647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute la galerie d’images ManyToMany des projets et migre featured_image.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_project_image (project_id INT NOT NULL, project_image_id INT NOT NULL, INDEX IDX_2F99AC7166D1F9C (project_id), INDEX IDX_2F99AC7EBAEB6 (project_image_id), PRIMARY KEY (project_id, project_image_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE project_image (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D6680DC1B548B0F (path), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE project_project_image ADD CONSTRAINT FK_2F99AC7166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_project_image ADD CONSTRAINT FK_2F99AC7EBAEB6 FOREIGN KEY (project_image_id) REFERENCES project_image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project DROP featured_image');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_project_image DROP FOREIGN KEY FK_2F99AC7166D1F9C');
        $this->addSql('ALTER TABLE project_project_image DROP FOREIGN KEY FK_2F99AC7EBAEB6');
        $this->addSql('DROP TABLE project_project_image');
        $this->addSql('DROP TABLE project_image');
        $this->addSql('ALTER TABLE project ADD featured_image VARCHAR(255) DEFAULT NULL');
    }
}
