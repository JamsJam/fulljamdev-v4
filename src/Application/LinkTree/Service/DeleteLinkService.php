<?php

namespace App\Application\LinkTree\Service;

use App\Application\LinkTree\Persister\LinkTreePersister;
use App\Application\LinkTree\Provider\LinkTreeProvider;

final readonly class DeleteLinkService
{
    public function __construct(private LinkTreeProvider $provider, private LinkTreePersister $persister)
    {
    }

    public function delete(int $id): void
    {
        $link = $this->provider->provideLink($id);
        if ($link) {
            $this->persister->removeLink($link);
        }
    }
}
