<?php

namespace App\Application\Blog\Article\Factory;

use App\Application\Blog\Article\Asset\ArticleCoverImageUploader;
use App\Application\Blog\Article\Dto\ArticleDto;
use App\Application\Blog\Category\Service\ResolveCategoryService;
use App\Entity\Blog\Article;
use App\Service\HtmlSanitizerService;

final readonly class ArticleFactory
{
    public function __construct(
        private HtmlSanitizerService $sanitizer,
        private ResolveCategoryService $categoryResolver,
        private ArticleCoverImageUploader $coverUploader,
    ) {
    }

    public function fromEntity(Article $article): ArticleDto
    {
        $dto = new ArticleDto();
        $dto->title = $article->getTitle();
        $dto->slug = $article->getSlug();
        $dto->categoryName = $article->getCategory()?->getName();
        $dto->summary = $article->getSummary();
        $dto->content = $article->getContent();
        $dto->coverImage = $article->getCoverImage();
        $dto->publishedAt = $article->getPublishedAt();

        return $dto;
    }

    public function create(ArticleDto $dto, ?Article $article = null): Article
    {
        $content = null === $dto->content ? null : $this->sanitizer->sanitize($dto->content);

        $coverImage = null === $dto->coverImageFile
            ? $dto->coverImage
            : $this->coverUploader->upload($dto->coverImageFile);

        return ($article ?? new Article())
            ->setTitle($dto->title)
            ->setSlug($dto->slug)
            ->setCategory($this->categoryResolver->resolve($dto->categoryName))
            ->setSummary($dto->summary)
            ->setContent($content)
            ->setCoverImage($coverImage)
            ->setPublishedAt($dto->publishedAt);
    }
}
