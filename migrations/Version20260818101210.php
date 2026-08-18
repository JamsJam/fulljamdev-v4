<?php

namespace DoctrineMigrations;

use App\Application\Reservation\Planner\Service\PlanningSlugGenerator;
use App\Service\SluggerService;
use App\Service\UuidGeneratorService;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260818101210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute un slug public unique et obligatoire aux plannings';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE planning ADD slug VARCHAR(92) DEFAULT NULL');
        $slugGenerator = new PlanningSlugGenerator(
            new SluggerService(new AsciiSlugger()),
            new UuidGeneratorService(),
        );

        /** @var array{id: int|string, title: string} $planning */
        foreach ($this->connection->fetchAllAssociative('SELECT id, title FROM planning') as $planning) {
            $this->addSql(
                'UPDATE planning SET slug = :slug WHERE id = :id',
                [
                    'slug' => $slugGenerator->generate($planning['title']),
                    'id' => $planning['id'],
                ],
            );
        }

        $this->addSql('ALTER TABLE planning MODIFY slug VARCHAR(92) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D499BFF6989D9B62 ON planning (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_D499BFF6989D9B62 ON planning');
        $this->addSql('ALTER TABLE planning DROP slug');
    }
}
