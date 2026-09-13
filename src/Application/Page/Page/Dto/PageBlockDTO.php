<?php

namespace App\Application\Page\Page\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PageBlockDTO
{
    public ?int $id = null;
    #[Assert\NotBlank]
    public string $type = '';
    public int $position = 0;
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^(?:[A-Za-z][A-Za-z0-9_:.-]*)?$/', message: 'L’identifiant doit commencer par une lettre et ne contenir que des lettres, chiffres, tirets, underscores, deux-points ou points.')]
    public ?string $anchorId = null;
    #[Assert\Valid]
    public object $data;

    public function __construct(?int $id = null, string $type = '', ?object $data = null, int $position = 0, ?string $anchorId = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data ?? new \stdClass();
        $this->position = $position;
        $this->anchorId = $anchorId;
    }
}
