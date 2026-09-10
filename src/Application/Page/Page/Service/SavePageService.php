<?php

namespace App\Application\Page\Page\Service;

use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Persister\PagePersister;
use App\Entity\Page\Page;

final readonly class SavePageService
{
    public function __construct(private PagePersister $persister)
    {
    }

    public function save(PageDTO $dto, ?Page $page = null): Page
    {
        return $this->persister->save($dto, $page);
    }
}
