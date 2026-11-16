<?php

namespace App\Application\LinkTree\Service;

use App\Application\LinkTree\Provider\LinkTreeProvider;
use App\Entity\LinkTree\LinkTree;

final readonly class GetPublicLinkTreeService
{
    public function __construct(private LinkTreeProvider $provider)
    {
    }

    /** @return array{tree: LinkTree, links: list<\App\Entity\LinkTree\Link>} */
    public function get(): array
    {
        $tree = $this->provider->provide();

        return ['tree' => $tree, 'links' => $this->provider->provideOnlineLinks($tree)];
    }
}
