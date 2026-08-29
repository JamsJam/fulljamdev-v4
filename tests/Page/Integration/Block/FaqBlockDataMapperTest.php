<?php

namespace App\Tests\Page\Integration\Block;

use App\Application\Page\Block\Library\Faq\Main\FaqDTO;
use App\Application\Page\Block\Mapper\BlockDataMapper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FaqBlockDataMapperTest extends KernelTestCase
{
    public function testItMapsFaqItems(): void
    {
        self::bootKernel();
        $mapper = self::getContainer()->get(BlockDataMapper::class);
        $data = [
            'title' => ['content' => 'Questions fréquentes', 'level' => 'h2', 'attributes' => []],
            'text' => ['content' => 'Les réponses aux questions les plus courantes.', 'attributes' => []],
            'items' => [
                ['question' => 'Quel est le délai ?', 'answer' => 'Le délai dépend du périmètre du projet.'],
                ['question' => 'Proposez-vous un suivi ?', 'answer' => 'Oui, un suivi peut être mis en place.'],
            ],
        ];

        $faq = $mapper->denormalize('faq.main', $data);

        self::assertInstanceOf(FaqDTO::class, $faq);
        self::assertCount(2, $faq->items);
        self::assertSame('Quel est le délai ?', $faq->items[0]->question);
        self::assertSame($data, $mapper->normalize($faq));
    }
}
