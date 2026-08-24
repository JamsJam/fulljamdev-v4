<?php

namespace App\Application\Page\Data\Dto;

use App\Application\Page\Data\Enum\ValueSource;
use Symfony\Component\Validator\Constraints as Assert;

final class NumberValueDTO
{
    public ValueSource $source = ValueSource::STATIC;
    #[Assert\PositiveOrZero]
    public ?int $value = null;
    public ?string $sourceKey = null;
}
