<?php

namespace App\Tests\Reservation\Application;

use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Planning;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AppointmentEmailActionTest extends WebTestCase
{
    #[DataProvider('actions')]
    public function testAdminMustValidateActionAndCannotApplyItTwice(string $action, AppointmentStatus $expected): void
    {
        [$client, $em, $appointment] = $this->prepare();
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');
        $client->loginUser($provider->loadUserByIdentifier('admin@example.test'));
        $path = '/dashboard/reservations/appointments/'.$appointment->getId().'/'.$action;
        $crawler = $client->request('GET', $path.'/review');
        self::assertResponseIsSuccessful();
        self::assertSame(AppointmentStatus::REQUESTED, $appointment->getStatus());
        $form = $crawler->filter('form[action="'.$path.'"]')->form();
        $client->submit($form);
        self::assertResponseRedirects();
        $em->clear();
        self::assertSame($expected, $em->find(Appointment::class, $appointment->getId())->getStatus());
        $client->request('GET', $path.'/review');
        self::assertResponseIsSuccessful();
        self::assertSelectorNotExists('form[action="'.$path.'"]');
        self::assertSelectorTextContains('section p[role="status"]', 'déjà été traitée');
    }

    public static function actions(): iterable
    {
        yield ['confirm', AppointmentStatus::CONFIRMED];
        yield ['reject', AppointmentStatus::REJECTED];
    }

    public function testAnonymousReadersMustLogInAndRegularUsersAreDenied(): void
    {
        [$client, , $appointment] = $this->prepare();
        $path = '/dashboard/reservations/appointments/'.$appointment->getId().'/confirm/review';
        $client->request('GET', $path);
        self::assertResponseRedirects();
        self::assertStringEndsWith('/login', $client->getResponse()->headers->get('Location'));
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');
        $client->loginUser($provider->loadUserByIdentifier('user@example.test'));
        $client->request('GET', $path);
        self::assertResponseStatusCodeSame(403);
    }

    public function testPostWithoutCsrfTokenCannotChangeAppointment(): void
    {
        [$client, , $appointment] = $this->prepare();
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');
        $client->loginUser($provider->loadUserByIdentifier('admin@example.test'));
        $client->request('POST', '/dashboard/reservations/appointments/'.$appointment->getId().'/reject');
        self::assertResponseStatusCodeSame(403);
        self::assertSame(AppointmentStatus::REQUESTED, $appointment->getStatus());
    }

    /** @return array{KernelBrowser, EntityManagerInterface, Appointment} */
    private function prepare(): array
    {
        $client = self::createClient();
        $client->disableReboot();
        self::getContainer()->set('doctrine.dbal.default_connection', DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]));
        $em = self::getContainer()->get(EntityManagerInterface::class);
        (new SchemaTool($em))->createSchema($em->getMetadataFactory()->getAllMetadata());
        $now = new \DateTimeImmutable('2026-01-01');
        $planning = (new Planning())->setTitle('Découverte')->setSlug('decouverte')->setDuration(30)->setGap(10)->setCreatedAt($now)->setEditedAt($now);
        $contact = (new Contact())->setFirstName('Ada')->setLastName('Lovelace')->setEmail('ada@example.test')->setPhoneNumber('')->setCreatedAt($now)->setUpdatedAt($now);
        $appointment = (new Appointment())->setPlanning($planning)->setContact($contact)->setTitle('Mon projet')->setCreatedAt($now)->setEditedAt($now)->setStartAt($now)->setEndAt($now->modify('+30 minutes'))->setLink('https://meet.google.com/abc-defg-hij');
        foreach ([$planning, $contact, $appointment] as $entity) {
            $em->persist($entity);
        }
        $em->flush();

        return [$client, $em, $appointment];
    }
}
