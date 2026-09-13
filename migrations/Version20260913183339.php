<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260913183339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crée les trois pages légales éditables.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE content_legal_page (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(50) NOT NULL, title VARCHAR(160) NOT NULL, content LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_A048EE65989D9B62 (slug), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql("INSERT INTO content_legal_page (slug, title, content) VALUES ('mention-legal', 'Mentions légales', '<p>À rédiger.</p>'), ('politique-confidentialite', 'Politique de confidentialité', '<p>À rédiger.</p>'), ('polotique-cookies', 'Politique relative aux cookies', '<p>À rédiger.</p>')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE content_legal_page');
    }
}
