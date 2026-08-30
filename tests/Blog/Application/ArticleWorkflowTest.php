<?php

namespace App\Tests\Blog\Application;

use App\Application\Blog\Article\Dto\ArticleDto;
use App\Application\Blog\Article\Form\ArticleType;
use App\Application\Blog\Workflow\Enum\ArticleStatus;
use App\Entity\Blog\Article;
use App\Entity\Blog\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;

final class ArticleWorkflowTest extends KernelTestCase
{
    public function testIncompleteDraftCanExistButCannotBeSubmitted(): void
    {
        $article = new Article();

        self::assertSame(ArticleStatus::DRAFT, $article->getStatus());
        self::assertFalse($this->workflow($article)->can($article, 'submit'));
    }

    public function testSlugAndPublicationDateAreNotEditableInTheArticleForm(): void
    {
        $form = static::getContainer()->get('form.factory')->create(ArticleType::class, new ArticleDto());

        self::assertFalse($form->has('slug'));
        self::assertFalse($form->has('publishedAt'));
        self::assertTrue($form->has('categoryName'));
        self::assertFalse($form->has('category'));
        self::assertTrue($form->has('coverImageFile'));
        self::assertFalse($form->has('coverImage'));
        self::assertFalse($form->has('seoTitle'));
        self::assertFalse($form->has('seoDescription'));
    }

    public function testCompleteDraftCanFollowTheEditorialWorkflow(): void
    {
        $article = $this->completeArticle();
        $workflow = $this->workflow($article);

        $workflow->apply($article, 'submit');
        self::assertSame(ArticleStatus::REVIEW, $article->getStatus());

        $workflow->apply($article, 'publish');
        self::assertSame(ArticleStatus::PUBLISHED, $article->getStatus());
        self::assertNotNull($article->getPublishedAt());

        $workflow->apply($article, 'take_offline');
        self::assertSame(ArticleStatus::REVIEW, $article->getStatus());
    }

    public function testScheduledArticleCanOnlyBePublishedOnceDue(): void
    {
        $article = $this->completeArticle()->setPublishedAt(new \DateTimeImmutable('+1 day'));
        $workflow = $this->workflow($article);
        $workflow->apply($article, 'submit');
        $workflow->apply($article, 'schedule');

        self::assertSame(ArticleStatus::SCHEDULED, $article->getStatus());
        self::assertFalse($workflow->can($article, 'publish'));

        $article->setPublishedAt(new \DateTimeImmutable('-1 minute'));
        self::assertTrue($workflow->can($article, 'publish'));
        $workflow->apply($article, 'publish');
        self::assertSame(ArticleStatus::PUBLISHED, $article->getStatus());
    }

    public function testArchivedArticleKeepsItsStateAndBlocksTransitionsUntilRestored(): void
    {
        $article = $this->completeArticle();
        $workflow = $this->workflow($article);
        $workflow->apply($article, 'submit');

        $article->archive();
        self::assertSame(ArticleStatus::REVIEW, $article->getStatus());
        self::assertFalse($workflow->can($article, 'publish'));

        $article->restore();
        self::assertSame(ArticleStatus::REVIEW, $article->getStatus());
        self::assertTrue($workflow->can($article, 'publish'));
    }

    public function testTitleAndSummaryLimitsAreCheckedOnSubmit(): void
    {
        $article = $this->completeArticle()
            ->setTitle(str_repeat('a', 51))
            ->setSummary(str_repeat('b', 161));

        self::assertFalse($this->workflow($article)->can($article, 'submit'));
    }

    private function completeArticle(): Article
    {
        return (new Article())
            ->setTitle('Un article complet')
            ->setSummary('Un résumé utile pour présenter le contenu.')
            ->setContent('<p>Le contenu de l’article.</p>')
            ->setCoverImage('cover.webp')
            ->setCategory((new Category())->setName('Symfony')->setSlug('symfony'));
    }

    private function workflow(Article $article): WorkflowInterface
    {
        return static::getContainer()->get(Registry::class)->get($article, 'article');
    }
}
