<?php

namespace App\Service;

use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

final readonly class HtmlSanitizerService
{
    public function __construct(
        private HtmlSanitizerInterface $htmlSanitizer,
    ) {
    }

    public function sanitize(string $text): string
    {
        return $this->htmlSanitizer->sanitize($text);
    }
}
