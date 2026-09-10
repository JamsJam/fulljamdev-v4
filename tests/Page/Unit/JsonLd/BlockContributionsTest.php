<?php

namespace App\Tests\Page\Unit\JsonLd;

use App\Application\Page\Block\Library\Blog\Latest\LatestArticlesDTO;
use App\Application\Page\Block\Library\Blog\Latest\LatestArticlesProvider;
use App\Application\Page\Block\Library\CardDisplay\Data\CardContentType;
use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayCardsProvider;
use App\Application\Page\Block\Library\CardDisplay\Data\CardDisplayItemDTO;
use App\Application\Page\Block\Library\CardDisplay\Data\FeaturedProjectsProviderInterface;
use App\Application\Page\Block\Library\CardDisplay\Shared\CardDisplayDTO;
use App\Application\Page\Block\Library\Faq\Main\FaqDTO;
use App\Application\Page\Block\Library\Faq\Main\FaqItemDTO;
use App\Application\Page\Data\Enum\ValueSource;
use App\Application\Page\Element\Cta\CtaTarget;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\SEO\JsonLd\Builder\BreadcrumbBuilder;
use App\Application\SEO\JsonLd\Builder\IdentityBuilder;
use App\Application\SEO\JsonLd\Builder\ItemListContributionBuilder;
use App\Application\SEO\JsonLd\Builder\JsonLdBuilder;
use App\Application\SEO\JsonLd\Builder\WebPageBuilder;
use App\Application\SEO\JsonLd\Context\PublicUrlGenerator;
use App\Application\SEO\JsonLd\Contributor\CardsJsonLdContributor;
use App\Application\SEO\JsonLd\Contributor\FaqJsonLdContributor;
use App\Application\SEO\JsonLd\Contributor\LatestArticlesJsonLdContributor;
use App\Application\SEO\JsonLd\Definition\WebPageDefinition;
use App\Application\SEO\JsonLd\Dto\IdentityData;
use App\Application\SEO\JsonLd\Dto\PageContext;
use App\Application\SEO\JsonLd\Enum\PageType;
use App\Application\SEO\JsonLd\Registry\BlockJsonLdCollector;
use App\Application\SEO\JsonLd\Registry\PageDefinitionRegistry;
use App\Entity\Blog\Article;
use App\Repository\Blog\ArticleRepository;
use App\Twig\Components\Page\Block\LatestArticles;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class BlockContributionsTest extends TestCase
{
    private const URL = 'https://example.com/page';

    public function testFaqBlocksMergeWithoutDuplicateQuestionsAndSerializeSafely(): void
    {
        $faq = $this->faq();
        $faq->items[] = new FaqItemDTO();
        $contributions = (new BlockJsonLdCollector([new FaqJsonLdContributor()]))->collect([
            new PageBlockDTO(1, 'faq.main', $faq), new PageBlockDTO(2, 'faq.main', $faq),
            new PageBlockDTO(3, 'unknown', $faq),
        ], self::URL);
        $context = new PageContext(PageType::STANDARD, self::URL, 'Page', contributions: $contributions);
        $graph = $this->builder()->build($context)['@graph'];
        self::assertCount(2, $graph);
        self::assertSame(['WebPage', 'FAQPage'], $graph[0]['@type']);
        self::assertSame([['@id' => $graph[1]['@id']]], $graph[0]['mainEntity']);
        $json = $this->builder()->serialize($context);
        self::assertStringNotContainsString('</script>', $json);
        self::assertSame($faq->items[0]->answer, json_decode($json, true)['@graph'][1]['acceptedAnswer']['text']);
    }

    public function testProfileKeepsItsMainEntityAndMentionsFaq(): void
    {
        $contribution = (new FaqJsonLdContributor())->contribute(new PageBlockDTO(1, 'faq.main', $this->faq()), self::URL.'#block-1');
        $identity = new IdentityData('Person', 'Auteur', 'https://example.com/#person');
        $context = new PageContext(PageType::STANDARD, self::URL, 'Profil', data: $identity, profilePage: true, contributions: [$contribution]);
        $page = $this->builder()->build($context)['@graph'][0];
        self::assertSame('ProfilePage', $page['@type']);
        self::assertSame(['@id' => $identity->id], $page['mainEntity']);
        self::assertSame([['@id' => $contribution->questions[0]]], $page['mentions']);
    }

    public function testEmptyFaqAndNonIndexablePagesProduceNoExtraMarkup(): void
    {
        $contribution = (new FaqJsonLdContributor())->contribute(new PageBlockDTO(1, 'faq.main', new FaqDTO()), self::URL.'#block-1');
        $context = new PageContext(PageType::STANDARD, self::URL, 'Page', contributions: [$contribution]);
        self::assertSame('WebPage', $this->builder()->build($context)['@graph'][0]['@type']);
        self::assertCount(1, $this->builder()->build($context)['@graph']);
        self::assertNull($this->builder()->serialize(new PageContext(PageType::STANDARD, self::URL, 'Page', indexable: false, contributions: [$contribution])));
    }

    public function testServicesAreExplicitAndDoNotShareTheirContactLinkAsIdentity(): void
    {
        $projects = $this->createStub(FeaturedProjectsProviderInterface::class);
        $contributor = new CardsJsonLdContributor(new CardDisplayCardsProvider($projects), $projects, $this->urls(), new ItemListContributionBuilder());
        $data = new CardDisplayDTO();
        foreach (['Développement', 'Accompagnement'] as $title) {
            $card = new CardDisplayItemDTO();
            $card->title = $title;
            $card->text = 'Description';
            $card->cta->label = 'Contact';
            $card->cta->href = '/contact';
            $data->cards[] = $card;
        }
        $block = new PageBlockDTO(1, 'services.main', $data);
        $generic = $contributor->contribute($block, self::URL.'#block-1');
        self::assertSame('Thing', $generic->nodes[1]['@type']);
        $data->contentType = CardContentType::SERVICE;
        $services = $contributor->contribute($block, self::URL.'#block-1');
        self::assertSame('Service', $services->nodes[1]['@type']);
        self::assertNotSame($services->nodes[1]['@id'], $services->nodes[2]['@id']);
        self::assertArrayNotHasKey('url', $services->nodes[1]);
        self::assertArrayNotHasKey('offers', $services->nodes[1]);
        self::assertSame([1, 2], array_column($services->nodes[0]['itemListElement'], 'position'));
    }

    public function testDynamicProjectCardsIgnoreStaticCardsAndDeduplicateAcrossBlocks(): void
    {
        $card = new CardDisplayItemDTO();
        $card->title = 'Projet publié';
        $card->cta->label = 'Voir';
        $card->cta->target = CtaTarget::ROUTE;
        $card->cta->routeName = 'app_front_project_show';
        $card->cta->routeParameters = ['slug' => 'projet'];
        $projects = $this->createStub(FeaturedProjectsProviderInterface::class);
        $projects->method('provide')->willReturn([$card]);
        $contributor = new CardsJsonLdContributor(new CardDisplayCardsProvider($projects), $projects, $this->urls(), new ItemListContributionBuilder());
        $data = new CardDisplayDTO();
        $data->source = ValueSource::DYNAMIC;
        $data->contentType = CardContentType::SERVICE;
        $data->cards = [new CardDisplayItemDTO()];
        $contributions = (new BlockJsonLdCollector([$contributor]))->collect([
            new PageBlockDTO(null, 'services.main', $data), new PageBlockDTO(null, 'card_display.with_image', $data),
        ], self::URL);
        $graph = $this->builder()->build(new PageContext(PageType::STANDARD, self::URL, 'Page', contributions: $contributions))['@graph'];
        self::assertCount(4, $graph); // page, two lists, one shared project
        self::assertSame('CreativeWork', $graph[2]['@type']);
        self::assertSame('https://example.com/projects/projet#project', $graph[2]['@id']);
        self::assertCount(2, $graph[0]['mentions']);
        $data->sourceKey = 'unsupported';
        self::assertSame([], $contributor->contribute(new PageBlockDTO(1, 'services.main', $data), self::URL.'#block-1')->nodes);
    }

    public function testInvalidCardRouteDoesNotBreakThePage(): void
    {
        $projects = $this->createStub(FeaturedProjectsProviderInterface::class);
        $contributor = new CardsJsonLdContributor(new CardDisplayCardsProvider($projects), $projects, $this->urls(), new ItemListContributionBuilder());
        $data = new CardDisplayDTO();
        $card = new CardDisplayItemDTO();
        $card->title = 'Carte';
        $card->cta->label = 'Voir';
        $card->cta->target = CtaTarget::ROUTE;
        $card->cta->routeName = 'missing';
        $data->cards = [$card];
        $result = $contributor->contribute(new PageBlockDTO(1, 'services.main', $data), self::URL.'#block-1');
        self::assertArrayNotHasKey('url', $result->nodes[1]);
    }

    public function testArticlesUseTheSameSelectionAsTheRenderedBlockPerRequest(): void
    {
        $article = new Article();
        $article->setTitle('Article public');
        $article->setSlug('article');
        $repository = new ArticleRepository($this->createStub(ManagerRegistry::class));
        $requests = new RequestStack();
        $requests->push(Request::create(self::URL));
        $requests->getCurrentRequest()->attributes->set(LatestArticlesProvider::class, [$article]);
        $provider = new LatestArticlesProvider($repository, $requests);
        $component = new LatestArticles($provider);
        $contributor = new LatestArticlesJsonLdContributor($provider, $this->urls(), new ItemListContributionBuilder());
        $block = new PageBlockDTO(1, 'blog.latest', new LatestArticlesDTO());
        $result = $contributor->contribute($block, self::URL.'#block-1');
        self::assertSame([$article], $component->getArticles());
        self::assertSame('https://example.com/blog/article#article', $result->nodes[1]['@id']);
        self::assertSame('BlogPosting', $result->nodes[1]['@type']);
        $requests->pop();
        $requests->push(Request::create(self::URL));
        $requests->getCurrentRequest()->attributes->set(LatestArticlesProvider::class, []);
        self::assertSame([], $component->getArticles());
    }

    private function faq(): FaqDTO
    {
        $faq = new FaqDTO();
        $item = new FaqItemDTO();
        $item->question = 'Quel délai ?';
        $item->answer = 'Selon le projet. </script><script>alert(1)</script>';
        $faq->items = [$item];

        return $faq;
    }

    private function builder(): JsonLdBuilder
    {
        return new JsonLdBuilder(new PageDefinitionRegistry([new WebPageDefinition(new WebPageBuilder(), new IdentityBuilder())]), new BreadcrumbBuilder());
    }

    private function urls(): PublicUrlGenerator
    {
        $routes = new RouteCollection();
        $routes->add('app_front_project_show', new Route('/projects/{slug}'));
        $routes->add('app_front_article_show', new Route('/blog/{slug}'));
        $requests = new RequestStack();
        $requests->push(Request::create(self::URL));

        return new PublicUrlGenerator(new UrlGenerator($routes, new RequestContext('', 'GET', 'example.com', 'https')), new Packages(), new UrlHelper($requests));
    }
}
