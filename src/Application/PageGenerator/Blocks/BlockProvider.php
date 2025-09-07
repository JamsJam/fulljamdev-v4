<?php

namespace App\Application\PageGenerator\Blocks;

use App\Application\PageGenerator\Blocks\Back\AdminSortableTableBlock;
use App\Application\PageGenerator\Blocks\Back\AdminTabsBlock;
use App\Service\ThemeService;

final class BlockProvider
{
    private ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function createBlock(array $config, array $params): BlockInterface
    {
        switch ($config['type']) {
            case 'adminTabsBlock':
                return new AdminTabsBlock(
                    $config['tabs'] ?? [],
                    $this->themeService->getTheme(),
                    $config['reverse'] ?? false
                );
            case 'adminSortableTableBlock':
                // searchandsort to get rows
                $rows = [];
                if ($config['isPaginated']) {
                    // paginatedService
                    // $maxPage = 0;
                }

                return new AdminSortableTableBlock(
                    $this->themeService->getTheme(),
                    $rows ?? [],
                    $config['isPaginated'] ?? false,
                    $config['reverse'] ?? false,
                    $config['colTitles'] ?? [],
                    $config['maxItems'] ?? 20,
                    $config['noItemsLabel'] ?? 'messae tableau vide',
                    $config['tableTitle'] ?? 'Titre du tableau',
                    $maxPage ?? null
                );

            default:
                throw new \Exception("Bloc inconnu : {$config['type']}");
        }
    }
}
