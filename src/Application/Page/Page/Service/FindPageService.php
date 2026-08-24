<?php

namespace App\Application\Page\Page\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Entity\Page\Page;

final readonly class FindPageService
{
    public function __construct(private PageProvider $provider)
    {
    }

    public function find(int $id): ?Page
    {
        return $this->provider->provideOne($id);
    }

    public function findByPath(string $path): ?Page
    {
        return $this->provider->provideOneByPath($path);
    }
}
