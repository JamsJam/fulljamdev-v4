<?php

namespace App\Application\LinkTree\Service;

use App\Application\LinkTree\Provider\LinkTreeProvider;
use App\Entity\LinkTree\LinkTree;

final readonly class GetLinkTreeService
{
    public function __construct(private LinkTreeProvider $provider)
    {
    }

    public function get(): LinkTree
    {
        return $this->provider->provide();
    }
}
