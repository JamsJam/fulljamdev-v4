<?php

namespace App\Application\Project\Service;

use App\Repository\Project\ProjectRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Clock\ClockInterface;

final readonly class BrowsePublishedProjectsService
{
    public function __construct(private ProjectRepository $projects, private ClockInterface $clock, private PaginatorInterface $paginator)
    {
    }

    /** @return PaginationInterface<int, \App\Entity\Project\Project> */
    public function browse(string $query = '', ?int $technologyId = null, int $page = 1): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->projects->createPublishedCatalogQuery(
                \DateTimeImmutable::createFromInterface($this->clock->now()),
                trim($query),
                $technologyId,
            ),
            max(1, $page),
            12,
            [PaginatorInterface::PAGE_OUT_OF_RANGE => PaginatorInterface::PAGE_OUT_OF_RANGE_FIX],
        );
    }
}
