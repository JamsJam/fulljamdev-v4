<?php

namespace App\Tests\Settings\Integration\Form;

use App\Application\Settings\Account\Dto\UserAccountDto;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Form\GeneralSettingsType;
use App\Form\UserAccountType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

final class SettingsFormTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    public function testMaintenanceCanBeEnabledWithoutHomepageAndWithLinks(): void
    {
        $settings = new GeneralSettingsDto();
        $form = $this->formFactory->create(GeneralSettingsType::class, $settings, ['csrf_protection' => false]);
        $form->submit([
            'siteTitle' => 'Portfolio', 'timezone' => 'Europe/Paris',
            'maintenanceEnabled' => '1', 'maintenanceMessage' => 'À bientôt !',
            'maintenanceLinks' => [['name' => 'Profil', 'value' => 'https://example.com/profil']],
        ]);
        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertTrue($settings->maintenanceEnabled);
        self::assertNull($settings->homepagePageId);
        self::assertSame('https://example.com/profil', $settings->maintenanceLinks[0]['value']);
    }

    public function testMaintenanceLinksRejectUnsafeSchemes(): void
    {
        $form = $this->formFactory->create(GeneralSettingsType::class, new GeneralSettingsDto(), ['csrf_protection' => false]);
        $form->submit([
            'siteTitle' => 'Portfolio', 'timezone' => 'Europe/Paris', 'maintenanceEnabled' => '1',
            'maintenanceLinks' => [['name' => 'Profil', 'value' => 'javascript:alert(1)']],
        ]);
        self::assertFalse($form->get('maintenanceLinks')->isValid());
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = self::getContainer()->get(FormFactoryInterface::class);
    }

    public function testUserAccountFormValidatesCredentialsUpdate(): void
    {
        $account = new UserAccountDto();
        $form = $this->formFactory->create(UserAccountType::class, $account, ['csrf_protection' => false]);
        $form->submit([
            'firstName' => 'Ada',
            'lastName' => 'Lovelace',
            'email' => 'ada@example.test',
            'phoneNumber' => '+33 1 23 45 67 89',
            'company' => 'Analytical Engines',
            'jobTitle' => 'Développeuse',
            'newPassword' => 'A-secure-password-123',
            'currentPassword' => 'Current-password-123',
            'submit' => '',
        ]);

        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame('A-secure-password-123', $account->newPassword);
    }

    public function testUserAccountFormRequiresCurrentPassword(): void
    {
        $form = $this->formFactory->create(UserAccountType::class, new UserAccountDto(), ['csrf_protection' => false]);
        $form->submit([
            'firstName' => 'Ada',
            'lastName' => 'Lovelace',
            'email' => 'ada@example.test',
            'phoneNumber' => '+33 1 23 45 67 89',
            'company' => 'Analytical Engines',
            'jobTitle' => 'Développeuse',
            'newPassword' => '',
            'currentPassword' => '',
            'submit' => '',
        ]);

        self::assertFalse($form->isValid());
        self::assertFalse($form->get('currentPassword')->isValid());
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
