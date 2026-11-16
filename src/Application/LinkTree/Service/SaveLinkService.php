<?php

namespace App\Application\LinkTree\Service;

use App\Application\LinkTree\Dto\LinkDto;
use App\Application\LinkTree\Persister\LinkTreePersister;
use App\Application\LinkTree\Provider\LinkTreeProvider;
use App\Entity\LinkTree\Link;
use App\Entity\LinkTree\LinkTree;

final readonly class SaveLinkService
{
    public function __construct(private LinkTreeProvider $provider, private LinkTreePersister $persister)
    {
    }

    public function add(LinkTree $tree, LinkDto $dto): void
    {
        if ('' === trim((string) $dto->label) || '' === trim((string) $dto->href)) {
            throw new \DomainException('Un titre et une adresse sont nécessaires pour ajouter un lien.');
        }
        $link = new Link();
        $link->setLabel(trim($dto->label))->setHref(trim($dto->href))->setIsOnline(false)->setIsPrefered(false);
        $tree->addLink($link);
        $this->persister->persist($tree);
    }

    public function update(int $id, LinkDto $dto): void
    {
        $link = $this->provider->provideLink($id) ?? throw new \DomainException('Ce lien n’existe plus.');
        $label = trim((string) $dto->label);
        $href = trim((string) $dto->href);
        if ($dto->isOnline && ('' === $label || '' === $href)) {
            throw new \DomainException('Un lien doit avoir un titre et une adresse pour être en ligne.');
        }
        $link->setLabel('' === $label ? null : $label)->setHref('' === $href ? null : $href)->setIsOnline($dto->isOnline)->setIsPrefered($dto->isPrefered);
        if ($link->isPrefered()) {
            foreach ($link->getLinkTree()->getLinks() as $other) {
                if ($other !== $link) {
                    $other->setIsPrefered(false);
                }
            }
        }
        $tree = $link->getLinkTree() ?? throw new \DomainException('Ce lien n’est rattaché à aucun LinkTree.');
        $this->persister->persist($tree);
    }
}
