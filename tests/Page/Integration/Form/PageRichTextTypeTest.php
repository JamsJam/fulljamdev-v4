<?php

namespace App\Tests\Page\Integration\Form;

use App\Application\Page\Block\Library\Faq\Main\FaqItemDTO;
use App\Application\Page\Block\Library\Faq\Main\FaqItemType;
use App\Application\Page\Element\Text\TextDTO;
use App\Application\Page\Element\Text\TextType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class PageRichTextTypeTest extends KernelTestCase
{
    public function testRichPageTextOnlyKeepsParagraphBoldAndItalic(): void
    {
        self::bootKernel();
        $dto = new TextDTO();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(TextType::class, $dto, [
            'csrf_protection' => false,
            'rich_text' => true,
        ]);

        self::assertSame('page-text', $form->get('content')->getConfig()->getOption('attr')['data-suneditor-profile-value']);

        $form->submit([
            'content' => '<h2>Titre interdit</h2><p>Un <strong>texte</strong> en <em>italique</em>.</p><ul><li>Liste interdite</li></ul><script>alert(1)</script>',
            'attributes' => [],
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertStringContainsString('<p>Un <strong>texte</strong> en <em>italique</em>.</p>', $dto->content);
        self::assertStringNotContainsString('<h2', $dto->content);
        self::assertStringNotContainsString('<ul', $dto->content);
        self::assertStringNotContainsString('<script', $dto->content);
    }

    public function testFaqAnswerUsesTheSameRestrictedEditor(): void
    {
        self::bootKernel();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(FaqItemType::class, new FaqItemDTO(), ['csrf_protection' => false]);

        self::assertSame('page-text', $form->get('answer')->getConfig()->getOption('attr')['data-suneditor-profile-value']);
    }
}
