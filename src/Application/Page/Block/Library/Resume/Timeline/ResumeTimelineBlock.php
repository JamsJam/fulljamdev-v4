<?php

namespace App\Application\Page\Block\Library\Resume\Timeline;

use App\Application\Page\Block\Interface\BlockDefinitionInterface;

final class ResumeTimelineBlock implements BlockDefinitionInterface
{
    public function type(): string
    {
        return 'resume.timeline';
    }

    public function label(): string
    {
        return 'resumeTimeline';
    }

    public function category(): string
    {
        return 'Contenu';
    }

    public function dtoClass(): string
    {
        return ResumeTimelineDTO::class;
    }

    public function formType(): string
    {
        return ResumeTimelineType::class;
    }

    public function component(): string
    {
        return 'Page:Block:Resume:Timeline';
    }

    public function formTemplate(): string
    {
        return 'dashboard/page/block/_resume_timeline_form.html.twig';
    }

    public function createDefaultData(): object
    {
        return new ResumeTimelineDTO();
    }
}
