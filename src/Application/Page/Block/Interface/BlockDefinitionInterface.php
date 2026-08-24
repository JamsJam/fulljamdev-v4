<?php

namespace App\Application\Page\Block\Interface;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.page_block')]
interface BlockDefinitionInterface
{
    public function type(): string;

    public function label(): string;

    public function category(): string;

    /** @return class-string */
    public function dtoClass(): string;

    /** @return class-string */
    public function formType(): string;

    public function component(): string;

    public function formTemplate(): string;

    public function createDefaultData(): object;
}
