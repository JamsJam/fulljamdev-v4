<?php

namespace App\Application\Page\Page\Provider;

use App\Entity\Page\Page;
use App\Repository\Page\PageRepository;

final readonly class PageProvider
{
    public function __construct(private PageRepository $repository)
    {
    }

    /** @return list<Page> */
    public function provide(): array
    {
        return $this->repository->findBy([], ['title' => 'ASC']);
    }

    public function provideOne(int $id): ?Page
    {
        return $this->repository->find($id);
    }

    public function provideOneByPath(string $path): ?Page
    {
        return $this->repository->findOneBy(['path' => $path]);
    }

    public function pathIsUsedByAnotherPage(string $path, ?Page $page = null): bool
    {
        $matchingPage = $this->provideOneByPath($path);

        return null !== $matchingPage && $matchingPage !== $page;
    }
}
