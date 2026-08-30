<?php

namespace App\Twig\Components\Page\Block;

use App\Application\Experience\Service\GetExperienceTimelineService;
use App\Application\Page\Block\Library\Resume\Timeline\ResumeTimelineDTO;
use App\Entity\Experience\Experience;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Page:Block:Resume:Timeline', template: 'components/page/block/resume/ResumeTimeline.html.twig')]
final class ResumeTimeline
{
    public ResumeTimelineDTO $data;
    public ?int $blockId = null;

    public function __construct(private readonly GetExperienceTimelineService $timeline)
    {
    }

    /** @return list<Experience> */
    public function getExperiences(): array
    {
        return $this->timeline->get();
    }
}
