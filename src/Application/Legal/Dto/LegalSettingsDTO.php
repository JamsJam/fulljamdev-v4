<?php

namespace App\Application\Legal\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class LegalSettingsDTO
{
    #[Assert\Valid]
    public LegalPageDTO $legalNotice;
    #[Assert\Valid]
    public LegalPageDTO $privacyPolicy;
    #[Assert\Valid]
    public LegalPageDTO $cookiePolicy;

    public function __construct()
    {
        $this->legalNotice = new LegalPageDTO();
        $this->privacyPolicy = new LegalPageDTO();
        $this->cookiePolicy = new LegalPageDTO();
    }
}
