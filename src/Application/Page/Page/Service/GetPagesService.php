<?php

namespace App\Application\Page\Page\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Entity\Page\Page;

final readonly class GetPagesService
{
    public function __construct(private PageProvider $provider)
    {
    }

    /** @return list<Page> */
    public function get(): array
    {
        return $this->provider->provide();
    }
}
