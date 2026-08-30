<?php

namespace App\Application\Blog\Article\Service;

use App\Repository\Blog\ArticleRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Clock\ClockInterface;

final readonly class BrowsePublishedArticlesService
{
    public function __construct(private ArticleRepository $articles, private ClockInterface $clock, private PaginatorInterface $paginator)
    {
    }

    /** @return PaginationInterface<int, \App\Entity\Blog\Article> */
    public function browse(string $query = '', string $categorySlug = '', int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->articles->createPublishedCatalogQuery(
                \DateTimeImmutable::createFromInterface($this->clock->now()),
                trim($query),
                trim($categorySlug),
            ),
            max(1, $page),
            12,
            [PaginatorInterface::PAGE_OUT_OF_RANGE => PaginatorInterface::PAGE_OUT_OF_RANGE_FIX],
        );
    }
}
