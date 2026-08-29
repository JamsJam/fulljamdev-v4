<?php

namespace App\Tests\Project\Integration\Form;

use App\Application\Project\Dto\ProjectDto;
use App\Application\Project\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class ProjectFormTest extends KernelTestCase
{
    public function testProjectFormConvertsTechnologiesToAList(): void
    {
        self::bootKernel();
        $dto = new ProjectDto();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(ProjectType::class, $dto, ['csrf_protection' => false]);
        $form->submit([
            'title' => 'Portfolio',
            'slug' => 'portfolio',
            'excerpt' => '',
            'content' => 'Présentation du projet',
            'featuredImage' => '',
            'technologies' => "Symfony\nTwig",
            'websiteUrl' => '',
            'repositoryUrl' => '',
            'isFeatured' => '1',
            'status' => 'draft',
            'publishedAt' => '',
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame(['Symfony', 'Twig'], $dto->technologies);
    }
}
