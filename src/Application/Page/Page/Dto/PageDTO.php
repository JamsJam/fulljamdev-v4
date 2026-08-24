<?php

namespace App\Application\Page\Page\Dto;

use App\Application\Page\SEO\Dto\SeoDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class PageDTO
{
    public ?int $id = null;
    #[Assert\NotBlank]
    #[Assert\Length(max: 160)]
    public string $title = '';
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    #[Assert\Regex(
        pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*(?:\/[a-z0-9]+(?:-[a-z0-9]+)*)*$/',
        message: 'Le chemin doit contenir des segments en minuscules séparés par des « / » (ex. services/developpement).',
    )]
    public string $path = '';
    /** @var array<int, PageBlockDTO> */
    #[Assert\Valid]
    public array $blocks = [];
    #[Assert\Valid]
    public SeoDTO $seo;

    public function __construct()
    {
        $this->seo = new SeoDTO();
    }
}
