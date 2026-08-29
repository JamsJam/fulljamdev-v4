<?php

namespace App\Application\Blog\Article\Factory;

use App\Application\Blog\Article\Dto\ArticleDto;
use App\Entity\Content\Article;
use App\Service\HtmlSanitizerService;

final readonly class ArticleFactory
{
    public function __construct(private HtmlSanitizerService $sanitizer)
    {
    }

    public function fromEntity(Article $article): ArticleDto
    {
        $dto = new ArticleDto();
        $dto->title = $article->getTitle();
        $dto->slug = $article->getSlug();
        $dto->category = $article->getCategory();
        $dto->excerpt = $article->getExcerpt();
        $dto->content = $article->getContent();
        $dto->featuredImage = $article->getFeaturedImage();
        $dto->status = $article->getStatus();
        $dto->publishedAt = $article->getPublishedAt();

        return $dto;
    }

    public function create(ArticleDto $dto, ?Article $article = null): Article
    {
        return ($article ?? new Article())->setTitle($dto->title)->setSlug($dto->slug)->setCategory($dto->category)->setExcerpt($dto->excerpt)->setContent($this->sanitizer->sanitize($dto->content))->setFeaturedImage($dto->featuredImage)->setStatus($dto->status)->setPublishedAt($dto->publishedAt);
    }
}
