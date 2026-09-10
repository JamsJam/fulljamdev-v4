<?php

namespace App\Application\SEO\JsonLd\Definition;

use App\Application\SEO\JsonLd\Builder\IdentityBuilder;
use App\Application\SEO\JsonLd\Builder\WebPageBuilder;
use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Dto\WebsiteData;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageDefinitionInterface;

final readonly class HomeDefinition implements PageDefinitionInterface
{
    public function __construct(private WebPageBuilder $webPage, private IdentityBuilder $identity)
    {
    }

    public function types(): array
    {
        return [PageType::HOME];
    }

    public function build(PageContext $context): array
    {
        $website = $context->data;
        if (!$website instanceof WebsiteData || '' === trim($website->name)) {
            throw new \InvalidArgumentException('The homepage requires WebsiteData with the public site name.');
        }

        $page = $this->webPage->build($context);
        $page['isPartOf'] = ['@id' => $website->url.'#website'];

        $websiteNode = [
            '@type' => 'WebSite',
            '@id' => $website->url.'#website',
            'url' => $website->url,
            'name' => $website->name,
        ];
        $nodes = [$page, $websiteNode];
        if (null !== $website->publisher) {
            $nodes[1]['publisher'] = ['@id' => $website->publisher->id];
            $nodes[] = $this->identity->build($website->publisher);
        }

        return $nodes;
    }
}
