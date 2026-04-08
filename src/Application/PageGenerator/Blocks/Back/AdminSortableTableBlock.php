<?php

namespace App\Application\PageGenerator\Blocks\Back;

use App\Application\PageGenerator\Blocks\BlockInterface;

final class AdminSortableTableBlock implements BlockInterface
{
    public function __construct(
        public string $theme,
        public array $rows,
        public bool $isPaginated,
        public bool $reverse,
        public array $colTitles,
        public int $maxItems,
        public string $noItemsLabel,
        public string $tableTitle,
        public ?int $maxPage,
    ) {
    }

    public function getType(): string
    {
        return 'adminSortableTableBlock';
    }

    public function getData(): array
    {
        return [
            'rows' => $this->rows,
            'theme' => $this->theme,
            'isPaginated' => $this->isPaginated,
            'reverse' => $this->reverse,
            'colTitles' => $this->colTitles,
            'maxItems' => $this->maxItems,
            'noItemsLabel' => $this->noItemsLabel,
            'tableTitle' => $this->tableTitle,
            'maxPage' => $this->maxPage,
        ];
    }

    public function getTemplate(): string
    {
        return 'partials/blocks/admin/AdminSortableTableBlock.html.twig';
    }
}
