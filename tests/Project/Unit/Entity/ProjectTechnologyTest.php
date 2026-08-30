<?php

namespace App\Tests\Project\Unit\Entity;

use App\Entity\Project\Project;
use App\Entity\Project\Technology;
use PHPUnit\Framework\TestCase;

final class ProjectTechnologyTest extends TestCase
{
    public function testTechnologyCanBeAssociatedWithAndRemovedFromAProject(): void
    {
        $project = new Project();
        $technology = (new Technology())->setName('Symfony');

        $project->addTechnology($technology);

        self::assertTrue($project->getTechnologies()->contains($technology));
        self::assertTrue($technology->getProjects()->contains($project));

        $project->removeTechnology($technology);

        self::assertFalse($project->getTechnologies()->contains($technology));
        self::assertFalse($technology->getProjects()->contains($project));
    }
}
