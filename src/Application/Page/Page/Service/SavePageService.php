<?php

namespace App\Application\Page\Page\Service;

use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Writer\PageWriter;
use App\Entity\Page\Page;

final readonly class SavePageService
{
    public function __construct(private PageWriter $writer)
    {
    }

    public function save(PageDTO $dto, ?Page $page = null): Page
    {
        return $this->writer->save($dto, $page);
    }
}
