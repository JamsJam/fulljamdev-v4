<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260914090000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Corrige le slug de la politique relative aux cookies.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE content_legal_page SET slug = 'politique-cookies' WHERE slug = 'polotique-cookies'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE content_legal_page SET slug = 'polotique-cookies' WHERE slug = 'politique-cookies'");
    }
}
