<?php

namespace App\Tests\Settings\Integration\Form;

use App\Application\Settings\Account\Dto\AccountSettingsDto;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Form\AccountSettingsType;
use App\Form\GeneralSettingsType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class SettingsFormTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = self::getContainer()->get(FormFactoryInterface::class);
    }

    public function testAccountFormMapsAndValidatesCompleteSettings(): void
    {
        $settings = new AccountSettingsDto();
        $form = $this->formFactory->create(AccountSettingsType::class, $settings, ['csrf_protection' => false]);
        $form->submit([
            'firstName' => 'Ada',
            'lastName' => 'Lovelace',
            'email' => 'ada@example.test',
            'phoneNumber' => '+33 1 23 45 67 89',
            'company' => 'Analytical Engines',
            'jobTitle' => 'Développeuse',
            'submit' => '',
        ]);

        self::assertTrue($form->isSubmitted());
        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame('ada@example.test', $settings->email);
    }

    public function testAccountFormRejectsMissingAndInvalidValues(): void
    {
        $form = $this->formFactory->create(AccountSettingsType::class, new AccountSettingsDto(), ['csrf_protection' => false]);
        $form->submit([
            'firstName' => '',
            'lastName' => '',
            'email' => 'not-an-email',
            'phoneNumber' => '',
            'company' => '',
            'jobTitle' => '',
            'submit' => '',
        ]);

        self::assertFalse($form->isValid());
        self::assertGreaterThanOrEqual(6, $form->getErrors(true)->count());
    }

    public function testGeneralFormMapsAValidTimezoneAndHomepageChoice(): void
    {
        $settings = new GeneralSettingsDto();
        $form = $this->formFactory->create(GeneralSettingsType::class, $settings, [
            'csrf_protection' => false,
            'page_choices' => ['Accueil' => 42, 'Services' => 84],
        ]);
        $form->submit([
            'siteTitle' => 'FullJam Dev',
            'timezone' => 'Europe/Paris',
            'homepagePageId' => '42',
            'submit' => '',
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame('Europe/Paris', $settings->timezone);
        self::assertSame(42, $settings->homepagePageId);
    }

    public function testGeneralFormRejectsUnknownTimezoneAndHomepageChoice(): void
    {
        $form = $this->formFactory->create(GeneralSettingsType::class, new GeneralSettingsDto(), [
            'csrf_protection' => false,
            'page_choices' => ['Accueil' => 42],
        ]);
        $form->submit([
            'siteTitle' => 'FullJam Dev',
            'timezone' => 'Mars/Olympus',
            'homepagePageId' => '999',
            'submit' => '',
        ]);

        self::assertFalse($form->isValid());
        self::assertFalse($form->get('timezone')->isValid());
        self::assertFalse($form->get('homepagePageId')->isValid());
    }
}
