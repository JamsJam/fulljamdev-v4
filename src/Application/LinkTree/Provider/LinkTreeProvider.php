<?php

namespace App\Application\LinkTree\Provider;

use App\Entity\LinkTree\Link;
use App\Entity\LinkTree\LinkTree;
use App\Repository\LinkTree\LinkRepository;
use App\Repository\LinkTree\LinkTreeRepository;

final readonly class LinkTreeProvider
{
    public function __construct(private LinkTreeRepository $trees, private LinkRepository $links)
    {
    }

    public function provide(): LinkTree
    {
        return $this->trees->findOneBy([]) ?? new LinkTree();
    }

    public function provideLink(int $id): ?Link
    {
        return $this->links->find($id);
    }

    /** @return list<Link> */
    public function provideOnlineLinks(LinkTree $tree): array
    {
        if (null === $tree->getId()) {
            return [];
        }

        return $this->links->findBy(['linkTree' => $tree, 'isOnline' => true], ['isPrefered' => 'DESC', 'id' => 'ASC']);
    }
}
