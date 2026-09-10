<?php

namespace App\Application\SEO\JsonLd\Definition;

use App\Application\SEO\JsonLd\Builder\IdentityBuilder;
use App\Application\SEO\JsonLd\Builder\WebPageBuilder;
use App\Application\SEO\JsonLd\Dto\IdentityData;
use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageDefinitionInterface;

final readonly class WebPageDefinition implements PageDefinitionInterface
{
    public function __construct(private WebPageBuilder $webPage, private IdentityBuilder $identity)
    {
    }

    public function types(): array
    {
        return [PageType::STANDARD, PageType::PROJECT, PageType::PLANNING, PageType::BLOG_CATALOG, PageType::PROJECT_CATALOG];
    }

    public function build(PageContext $context): array
    {
        // Catalogues group real content, but do not qualify for a Google carousel.
        $schemaType = $context->profilePage && $context->data instanceof IdentityData
            ? 'ProfilePage'
            : (in_array($context->type, [PageType::BLOG_CATALOG, PageType::PROJECT_CATALOG], true)
            ? 'CollectionPage'
            : 'WebPage');

        $page = $this->webPage->build($context, $schemaType);
        if ('ProfilePage' !== $schemaType) {
            return [$page];
        }

        $page['mainEntity'] = ['@id' => $context->data->id];

        return [$page, $this->identity->build($context->data)];
    }
}
