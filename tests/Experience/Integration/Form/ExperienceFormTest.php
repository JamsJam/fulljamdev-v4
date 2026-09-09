<?php

namespace App\Tests\Experience\Integration\Form;

use App\Application\Experience\Dto\ExperienceDto;
use App\Application\Experience\Factory\ExperienceFactory;
use App\Application\Experience\Form\ExperienceType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class ExperienceFormTest extends KernelTestCase
{
    public function testExperienceFormAcceptsFormattedRealizations(): void
    {
        self::bootKernel();
        $dto = new ExperienceDto();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(ExperienceType::class, $dto, ['csrf_protection' => false]);
        $form->submit([
            'title' => 'Développeur Symfony',
            'company' => 'Fulljamdev',
            'type' => 'Freelance',
            'contractType' => '',
            'beginAt' => '2026-01-01',
            'endAt' => '',
            'about' => '<p><strong>API Platform</strong></p><ul><li>Tests automatisés</li></ul>',
            'isVisible' => '1',
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame('<p><strong>API Platform</strong></p><ul><li>Tests automatisés</li></ul>', $dto->about);
    }

    public function testExperienceRealizationsAreSanitizedBeforePersistence(): void
    {
        self::bootKernel();
        $dto = new ExperienceDto();
        $dto->title = 'Développeur Symfony';
        $dto->company = 'Fulljamdev';
        $dto->type = 'Freelance';
        $dto->beginAt = new \DateTimeImmutable('2026-01-01');
        $dto->about = '<p onclick="alert(1)"><strong>API</strong> <a href="https://example.com">Platform</a></p><h2>Titre interdit</h2><script>alert(1)</script>';

        $experience = self::getContainer()->get(ExperienceFactory::class)->create($dto);

        self::assertSame('<p><strong>API</strong> </p>', $experience->getAbout());
    }
}
