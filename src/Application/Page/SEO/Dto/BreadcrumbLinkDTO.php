<?php

namespace App\Application\Page\SEO\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class BreadcrumbLinkDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    public string $label = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 2048)]
    #[Assert\Regex(pattern: '#^(?:https?://[^\s]+|/[^\s]*)$#i', message: 'Saisissez une URL HTTP(S) ou un chemin commençant par /.')]
    public string $url = '';
}
