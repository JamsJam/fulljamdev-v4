<?php

namespace App\Application\LinkTree\Persister;

use App\Entity\LinkTree\Link;
use App\Entity\LinkTree\LinkTree;
use Doctrine\ORM\EntityManagerInterface;

final readonly class LinkTreePersister
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function persist(LinkTree $tree): void
    {
        $this->entityManager->persist($tree);
        $this->entityManager->flush();
    }

    public function removeLink(Link $link): void
    {
        $this->entityManager->remove($link);
        $this->entityManager->flush();
    }
}
