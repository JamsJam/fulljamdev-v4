<?php

namespace App\Application\Page\Data\Interface;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.page_dynamic_value')]
interface DynamicValueProviderInterface
{
    public function key(): string;

    public function resolve(): int|float|string|bool|null;
}
