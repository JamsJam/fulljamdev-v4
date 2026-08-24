<?php

namespace App\Application\Page\Page\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PageBlockDTO
{
    public ?int $id = null;
    #[Assert\NotBlank]
    public string $type = '';
    #[Assert\Valid]
    public object $data;

    public function __construct(?int $id = null, string $type = '', ?object $data = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->data = $data ?? new \stdClass();
    }
}
