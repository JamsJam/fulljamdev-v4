<?php

namespace App\Application\SEO\JsonLd\Context;

use App\Application\Page\Page\Dto\PageDTO;
use App\Application\SEO\JsonLd\Dto\BreadcrumbItem;
use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Dto\WebsiteData;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Interface\PageContextProviderInterface;
use App\Application\Settings\Service\GetGeneralSettingsService;

final readonly class BuiltPageContextProvider implements PageContextProviderInterface
{
    public function __construct(
        private PublicUrlGenerator $urls,
        private GetGeneralSettingsService $settings,
        private StructuredIdentityProvider $identity,
    ) {
    }

    public function routes(): array
    {
        return ['app_home', 'app_front_page'];
    }

    public function provide(string $route, array $view): ?PageContext
    {
        $page = $view['page'] ?? null;
        if (!$page instanceof PageDTO) {
            return null;
        }

        $home = 'app_home' === $route;
        $url = $this->urls->canonical($page->seo->canonicalUrl, $this->urls->route($route, $home ? [] : ['path' => $page->path]));
        $identity = $this->identity->provide();
        $breadcrumbs = [];
        foreach ($page->seo->breadcrumbParents as $parent) {
            $parentUrl = $this->urls->optionalAbsolute($parent->url);
            if ('' !== trim($parent->label) && null !== $parentUrl) {
                $breadcrumbs[] = new BreadcrumbItem(trim($parent->label), $parentUrl);
            }
        }

        return new PageContext(
            type: $home ? PageType::HOME : PageType::STANDARD,
            url: $url,
            title: $page->seo->title ?: $page->title,
            description: $page->seo->description,
            data: $home ? new WebsiteData($this->settings->get()->siteTitle, $this->urls->route('app_home'), $identity) : ($page->seo->profilePage ? $identity : null),
            indexable: !$page->seo->noIndex,
            breadcrumbParents: $breadcrumbs,
            profilePage: $page->seo->profilePage,
            breadcrumbName: $page->title,
        );
    }
}
