<?php

namespace App\Application\LinkTree\Service;

use App\Application\LinkTree\Asset\LinkTreePhotoUploader;
use App\Application\LinkTree\Dto\LinkTreeDto;
use App\Application\LinkTree\Persister\LinkTreePersister;
use App\Entity\LinkTree\LinkTree;

final readonly class SaveLinkTreeProfileService
{
    public function __construct(private LinkTreePhotoUploader $uploader, private LinkTreePersister $persister)
    {
    }

    public function save(LinkTree $tree, LinkTreeDto $dto): void
    {
        $tree->setName($dto->name ? trim($dto->name) : null)->setCompany($dto->company ? trim($dto->company) : null);
        if ($dto->profilePictureFile) {
            $tree->setProfilePicture('/uploads/linktree/'.$this->uploader->upload($dto->profilePictureFile));
        }
        $this->persister->persist($tree);
    }
}
