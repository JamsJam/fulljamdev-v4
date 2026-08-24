<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260819234514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les pages et leurs blocs marketing ordonnés';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(160) NOT NULL, path VARCHAR(180) NOT NULL, seo JSON NOT NULL, UNIQUE INDEX UNIQ_D9685BE5B548B0F (path), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE content_page_block (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(100) NOT NULL, position INT NOT NULL, data JSON NOT NULL, page_id INT NOT NULL, INDEX IDX_7CA3DE06C4663E4 (page_id), INDEX page_block_position_idx (page_id, position), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE content_page_block ADD CONSTRAINT FK_7CA3DE06C4663E4 FOREIGN KEY (page_id) REFERENCES content_page (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content_page_block DROP FOREIGN KEY FK_7CA3DE06C4663E4');
        $this->addSql('DROP TABLE content_page');
        $this->addSql('DROP TABLE content_page_block');
    }
}
