<?php

namespace App\Application\Experience\Factory;

use App\Application\Experience\Dto\ExperienceDto;
use App\Application\Experience\Service\ExperienceContentSanitizer;
use App\Entity\Content\Experience;

final readonly class ExperienceFactory
{
    public function __construct(
        private ExperienceContentSanitizer $contentSanitizer,
    ) {
    }

    public function fromEntity(Experience $experience): ExperienceDto
    {
        $dto = new ExperienceDto();
        $dto->title = $experience->getTitle();
        $dto->company = $experience->getCompany();
        $dto->type = $experience->getType();
        $dto->contractType = $experience->getContractType();
        $dto->beginAt = $experience->getBeginAt();
        $dto->endAt = $experience->getEndAt();
        $dto->about = $experience->getAbout();
        $dto->isVisible = $experience->isVisible();

        return $dto;
    }

    public function create(ExperienceDto $dto, ?Experience $experience = null): Experience
    {
        return ($experience ?? new Experience())->setTitle($dto->title)->setCompany($dto->company)->setType($dto->type)->setContractType($dto->contractType)->setBeginAt($dto->beginAt ?? throw new \LogicException('La date de début est obligatoire.'))->setEndAt($dto->endAt)->setAbout($this->contentSanitizer->sanitize($dto->about))->setIsVisible($dto->isVisible);
    }
}
