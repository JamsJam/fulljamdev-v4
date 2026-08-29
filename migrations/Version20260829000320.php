<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260829000320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convertit les réalisations des expériences du JSON vers du HTML limité';
    }

    public function up(Schema $schema): void
    {
        $table = $this->connection->createSchemaManager()->introspectTable('content_experience');
        if (!$table->hasColumn('about_html')) {
            $this->connection->executeStatement('ALTER TABLE content_experience ADD about_html LONGTEXT DEFAULT NULL');
        }

        foreach ($this->connection->fetchAllAssociative('SELECT id, about FROM content_experience') as $experience) {
            $about = (string) $experience['about'];
            $items = json_decode($about, true);

            if (\is_array($items)) {
                $html = '<ul>';
                foreach ($items as $item) {
                    $html .= '<li>'.htmlspecialchars((string) $item, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</li>';
                }
                $html .= '</ul>';
            } else {
                $html = $about;
            }

            $this->connection->update('content_experience', ['about_html' => $html], ['id' => $experience['id']]);
        }

        $this->connection->executeStatement('ALTER TABLE content_experience DROP about');
        $this->connection->executeStatement('ALTER TABLE content_experience CHANGE about_html about LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeStatement('ALTER TABLE content_experience ADD about_json JSON DEFAULT NULL');

        foreach ($this->connection->fetchAllAssociative('SELECT id, about FROM content_experience') as $experience) {
            $text = trim(strip_tags((string) $experience['about']));
            $json = json_encode('' === $text ? [] : [$text], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $this->connection->update('content_experience', ['about_json' => $json], ['id' => $experience['id']]);
        }

        $this->connection->executeStatement('ALTER TABLE content_experience DROP about');
        $this->connection->executeStatement('ALTER TABLE content_experience CHANGE about_json about JSON NOT NULL');
    }
}
