<?php

namespace App\Tests\Reservation\Integration\Scenario;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

final class AppointmentEmailTemplateTest extends KernelTestCase
{
    public function testAdminEmailContainsBothAbsoluteActionLinksInHtmlAndText(): void
    {
        self::bootKernel();
        $router = self::getContainer()->get(RouterInterface::class);
        $router->getContext()->setHost('fulljamdev.fr');
        $router->getContext()->setScheme('https');
        $twig = self::getContainer()->get(Environment::class);
        $context = $this->context();
        $html = $twig->render('emails/reservation/appointment_requested_user.html.twig', $context);
        $text = $twig->render('emails/reservation/appointment_requested_user.txt.twig', $context);
        $crawler = new Crawler($html);

        foreach (['confirm', 'reject'] as $action) {
            $url = 'https://fulljamdev.fr/dashboard/reservations/appointments/42/'.$action.'/review';
            self::assertCount(1, $crawler->filter('a[href="'.$url.'"]'));
            self::assertStringContainsString($url, $text);
        }
        self::assertCount(0, $crawler->filter('form, script'));
        self::assertCount(1, $crawler->filter('meta[name="viewport"]'));
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('09:00', $html);

        $contactHtml = $twig->render('emails/reservation/appointment_requested_contact.html.twig', $context);
        self::assertStringNotContainsString('/dashboard/', $contactHtml);
    }

    public function testLifecycleAndReminderEmailsRenderActionsAndSanitizeSummary(): void
    {
        self::bootKernel();
        $twig = self::getContainer()->get(Environment::class);
        $context = $this->context();
        foreach (['day_before', 'hour_before'] as $type) {
            $html = $twig->render('emails/reservation/reminder_'.$type.'.html.twig', $context);
            self::assertStringContainsString('href="https://meet.google.com/abc-defg-hij"', $html);
        }
        $context['notification'] = ['subject' => 'Compte rendu', 'heading' => 'Compte rendu', 'message' => 'Merci pour cet échange.', 'action' => null];
        $context['appointment']['summary'] = '<p><strong>Décision</strong></p><script>alert(1)</script>';
        $html = $twig->render('emails/reservation/appointment_lifecycle.html.twig', $context);
        self::assertStringContainsString('<strong>Décision</strong>', $html);
        self::assertStringNotContainsString('<script', $html);
    }

    /** @return array<string, mixed> */
    private function context(): array
    {
        return ['appointment' => [
            'id' => 42, 'title' => 'Présentation <script>test</script>',
            'timezone' => 'Europe/Paris', 'startAt' => new \DateTimeImmutable('2026-10-01 09:00 Europe/Paris'),
            'endAt' => new \DateTimeImmutable('2026-10-01 09:30 Europe/Paris'),
            'planning' => ['title' => 'Appel découverte'], 'planningSlug' => 'appel-decouverte',
            'contact' => ['firstName' => 'Ada', 'lastName' => 'Lovelace', 'email' => 'ada@example.test', 'phoneNumber' => ''],
            'contactFirstName' => 'Ada', 'link' => 'https://meet.google.com/abc-defg-hij', 'summary' => null,
        ]];
    }
}
