<?php

namespace App\Application\Page\Block\Library\Resume\Timeline;

use App\Application\Page\Element\Heading\HeadingDTO;
use App\Application\Page\Element\Heading\HeadingLevel;
use Symfony\Component\Validator\Constraints as Assert;

final class ResumeTimelineDTO
{
    #[Assert\Valid]
    public HeadingDTO $title;

    public function __construct()
    {
        $this->title = new HeadingDTO();
        $this->title->level = HeadingLevel::H2;
    }
}
