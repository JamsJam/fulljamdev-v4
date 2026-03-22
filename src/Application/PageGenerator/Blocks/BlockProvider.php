<?php

namespace App\Application\PageGenerator\Blocks;

use App\Service\ThemeService;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;


final class BlockProvider
{
    

    public function __construct(
        private ThemeService $themeService,

        //? instancie dynamiquement le block avec le bon service
        #[AutowireLocator([

        ])]
        private ContainerInterface $services
    ){}

    public function createBlock(array $config, array $params ): BlockInterface
    {
        // add case to the switch
        switch ($config['type']) {


            default:
                throw new \Exception("Bloc inconnu : {$config['type']}");
        }
    }
}
