<?php

namespace App\Tests\Project\Integration\Form;

use App\Application\Project\Dto\ProjectDto;
use App\Application\Project\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class ProjectFormTest extends KernelTestCase
{
    public function testProjectFormUsesACreatableTechnologyAutocomplete(): void
    {
        self::bootKernel();
        $dto = new ProjectDto();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(ProjectType::class, $dto, ['csrf_protection' => false]);
        $form->submit([
            'title' => 'Portfolio',
            'excerpt' => '',
            'content' => 'Présentation du projet',
            'technologies' => '',
            'websiteUrl' => '',
            'repositoryUrl' => '',
            'isFeatured' => '1',
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame([], $dto->technologies);
        self::assertSame('technology-select', $form->get('technologies')->getConfig()->getOption('attr')['data-controller']);
        self::assertStringContainsString('/dashboard/projet/technologie/autocomplete', $form->get('technologies')->getConfig()->getOption('attr')['data-technology-select-url-value']);
        self::assertFalse($form->has('slug'));
        self::assertFalse($form->has('status'));
        self::assertFalse($form->has('publishedAt'));
        self::assertTrue($form->get('imageFiles')->getConfig()->getOption('multiple'));
    }
}
