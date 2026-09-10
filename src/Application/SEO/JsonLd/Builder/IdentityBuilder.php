<?php

namespace App\Application\SEO\JsonLd\Builder;

use App\Application\SEO\JsonLd\Dto\IdentityData;

final readonly class IdentityBuilder
{
    /** @return array<string, mixed> */
    public function build(IdentityData $identity): array
    {
        $node = [
            '@type' => $identity->type,
            '@id' => $identity->id,
            'name' => $identity->name,
        ];
        if (null !== $identity->url) {
            $node['url'] = $identity->url;
        }
        if ('Organization' === $identity->type && null !== $identity->logo) {
            $node['logo'] = $identity->logo;
        }
        if ([] !== $identity->sameAs) {
            $node['sameAs'] = $identity->sameAs;
        }

        return $node;
    }
}
