<?php

namespace App\Application\PageGenerator\Blocks;

/**
 * Undocumented interface
 */
interface BlockInterface
{
    public function getType(): string; // block name to call

    public function getData(): array; // data that goes to twig

    public function getTemplate(): string; // twig template to call
}
