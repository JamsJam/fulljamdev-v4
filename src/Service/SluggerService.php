<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class SluggerService
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function slugify(string $value, ?int $maximumLength = null): string
    {
        $slug = strtolower((string) $this->slugger->slug($value));

        return null === $maximumLength ? $slug : substr($slug, 0, $maximumLength);
    }
}
