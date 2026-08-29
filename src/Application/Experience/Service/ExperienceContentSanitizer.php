<?php

namespace App\Application\Experience\Service;

use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

final readonly class ExperienceContentSanitizer
{
    public function __construct(
        #[Target('app.experience_sanitizer')]
        private HtmlSanitizerInterface $sanitizer,
    ) {
    }

    public function sanitize(string $content): string
    {
        return $this->sanitizer->sanitize($content);
    }
}
