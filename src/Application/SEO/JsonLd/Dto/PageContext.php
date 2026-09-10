<?php

namespace App\Application\SEO\JsonLd\Dto;

use App\Application\SEO\JsonLd\Enum\PageType;

/** Only public presentation data belongs here, never entities or booking/contact data. */
final readonly class PageContext
{
    /**
     * @param list<string>         $images            Absolute image URLs
     * @param list<BreadcrumbItem> $breadcrumbParents Ordered parent pages; the current page is appended automatically
     */
    public function __construct(
        public PageType $type,
        public string $url,
        public string $title,
        public ?string $description = null,
        public array $images = [],
        public ?object $data = null,
        public bool $indexable = true,
        public array $breadcrumbParents = [],
        public bool $profilePage = false,
        public ?string $breadcrumbName = null,
    ) {
        if (!filter_var($url, FILTER_VALIDATE_URL) || !in_array(parse_url($url, PHP_URL_SCHEME), ['https', 'http'], true) || null !== parse_url($url, PHP_URL_FRAGMENT)) {
            throw new \InvalidArgumentException('A JSON-LD page URL must be absolute (HTTP/HTTPS), without a fragment.');
        }
    }

    public function id(string $fragment): string
    {
        return $this->url.'#'.$fragment;
    }
}
