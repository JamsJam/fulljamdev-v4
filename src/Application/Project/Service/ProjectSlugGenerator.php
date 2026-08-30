<?php

namespace App\Application\Project\Service;

use App\Application\Project\Provider\ProjectProvider;
use App\Entity\Project\Project;
use App\Service\SluggerService;

final readonly class ProjectSlugGenerator
{
    private const MAX_LENGTH = 200;

    public function __construct(private SluggerService $slugger, private ProjectProvider $projects)
    {
    }

    public function generate(Project $project): void
    {
        if ('' !== $project->getSlug()) {
            return;
        }

        $base = $this->slugger->slugify($project->getTitle(), self::MAX_LENGTH) ?: 'projet';
        $slug = $base;
        $suffix = 2;
        while ($this->projects->slugExists($slug, $project->getId())) {
            $ending = '-'.$suffix++;
            $slug = substr($base, 0, self::MAX_LENGTH - strlen($ending)).$ending;
        }
        $project->setSlug($slug);
    }
}
