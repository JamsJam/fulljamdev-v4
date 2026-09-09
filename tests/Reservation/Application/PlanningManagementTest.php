<?php

namespace App\Tests\Reservation\Application;

use App\Application\Page\Block\Library\Planning\Main\PlanningBlockDTO;
use App\Application\Reservation\Appointment\Enum\AppointmentStatus;
use App\Application\Reservation\Appointment\Resolver\PublicSlotResolver;
use App\Application\Reservation\Planner\Service\FindPlanningService;
use App\Application\Reservation\Planner\Service\PlanningInvitationService;
use App\Application\Settings\General\Cache\GeneralSettingsCache;
use App\Application\Settings\General\Dto\GeneralSettingsDto;
use App\Entity\Contact;
use App\Entity\Reservation\Appointment;
use App\Entity\Reservation\Availability;
use App\Entity\Reservation\Planning;
use App\Twig\Components\Page\Block\PlanningBlock;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class PlanningManagementTest extends WebTestCase
{
    public function testEditAndDeletePreserveRequestedAppointments(): void
    {
        [$client, $em, $planning] = $this->prepare();
        $now = new \DateTimeImmutable();
        $contact = (new Contact())->setFirstName('Ada')->setLastName('Lovelace')->setEmail('ada@example.test')->setPhoneNumber('')->setCreatedAt($now)->setUpdatedAt($now);
        $appointment = (new Appointment())->setPlanning($planning)->setContact($contact)->setTitle('Projet')->setStartAt($now->modify('+1 day'))->setEndAt($now->modify('+1 day +30 minutes'))->setCreatedAt($now)->setEditedAt($now);
        $em->persist($contact);
        $em->persist($appointment);
        $em->flush();
        $id = $planning->getId();
        $appointmentId = $appointment->getId();
        $start = $appointment->getStartAt();
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');
        $client->loginUser($provider->loadUserByIdentifier('admin@example.test'));
        $crawler = $client->request('GET', '/dashboard/reservations/plannings/'.$id.'/edit');
        self::assertResponseIsSuccessful();
        $client->submit($crawler->selectButton('Enregistrer les modifications')->form([
            'planning[title]' => 'Nouveau titre', 'planning[duration]' => 45,
            'planning[isActive]' => '1', 'planning[isOnline]' => '1',
        ]));
        self::assertResponseRedirects('/dashboard/reservations/plannings/', 303);
        $em->clear();
        $saved = $em->find(Planning::class, $id);
        self::assertSame('Nouveau titre', $saved->getTitle());
        self::assertSame(45, $saved->getDuration());
        self::assertTrue($saved->isActive());
        self::assertTrue($saved->isOnline());
        self::assertSame('private', $saved->getSlug());
        self::assertCount(1, $saved->getAvailabilities());
        $client->request('POST', '/dashboard/reservations/plannings/'.$id.'/delete');
        self::assertResponseStatusCodeSame(403);
        $crawler = $client->request('GET', '/dashboard/reservations/plannings/'.$id.'/delete');
        self::assertResponseIsSuccessful();
        $client->submit($crawler->filter('form')->form());
        self::assertResponseRedirects('/dashboard/reservations/plannings/', 303);
        $em->clear();
        $savedAppointment = $em->find(Appointment::class, $appointmentId);
        self::assertSame(AppointmentStatus::REQUESTED, $savedAppointment->getStatus());
        self::assertSame($start->getTimestamp(), $savedAppointment->getStartAt()->getTimestamp());
        self::assertSame($id, $savedAppointment->getPlanning()->getId());
        self::assertNotNull($savedAppointment->getPlanning()->getArchivedAt());
        self::assertNull(self::getContainer()->get(FindPlanningService::class)->find($id));
    }

    #[DataProvider('planningTimezones')]
    public function testPrivateBookingRequiresInvitationOnEveryEndpointAndKeepsItInForms(string $timezone): void
    {
        [$client, , $planning] = $this->prepare($timezone);
        $token = self::getContainer()->get(PlanningInvitationService::class)->create($planning, 24);
        $date = new \DateTimeImmutable('tomorrow', new \DateTimeZone($timezone));
        $month = new \DateTimeImmutable($date->format('Y-m-01'), new \DateTimeZone('UTC'));
        $slots = self::getContainer()->get(PublicSlotResolver::class)->resolveMonth($planning, $month);
        self::assertContains('09:00', $slots[$date->format('Y-m-d')] ?? []);
        foreach (['', '/calendar/'.$date->format('Y-m'), '/times/'.$date->format('Y-m-d').'?timezone=Europe/Paris', '/confirmation'] as $suffix) {
            $path = '/book-meeting/private'.$suffix;
            $client->request('GET', $path);
            self::assertResponseStatusCodeSame(404);
            $client->request('GET', $path.(str_contains($path, '?') ? '&' : '?').'access='.$token);
            self::assertResponseIsSuccessful();
        }
        $crawler = $client->request('GET', '/book-meeting/private?access='.$token);
        self::assertStringContainsString('access='.$token, $crawler->filter('form')->attr('action'));
        self::assertStringContainsString('access='.$token, $crawler->filter('a[href*="/calendar/"]')->first()->attr('href'));
        $csrf = $crawler->filter('input[name="public_appointment[_token]"]')->attr('value');
        $client->request('POST', '/book-meeting/private?access='.$token, [
            '_booking_step' => 'submit',
            'public_appointment' => [
                '_token' => $csrf,
                'date' => ['value' => $date->format('Y-m-d')],
                'time' => ['value' => '09:00', 'timezone' => 'Europe/Paris'],
                'contact' => ['firstName' => 'Ada', 'lastName' => 'Lovelace', 'email' => 'ada@example.test', 'phoneNumber' => '0601020304', 'reason' => 'Mon projet'],
            ],
        ]);
        self::assertResponseRedirects();
        self::assertStringContainsString('access='.$token, $client->getResponse()->headers->get('Location'));
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertCount(1, self::getContainer()->get(EntityManagerInterface::class)->getRepository(Appointment::class)->findAll());
        $client->request('POST', '/book-meeting/private', ['_booking_step' => 'submit']);
        self::assertResponseStatusCodeSame(404);
        $client->request('GET', '/book-meeting/private?access=forged');
        self::assertResponseStatusCodeSame(404);
    }

    public static function planningTimezones(): iterable
    {
        yield 'UTC' => ['UTC'];
        yield 'positive UTC offset' => ['Europe/Paris'];
        yield 'negative UTC offset' => ['America/New_York'];
    }

    public function testAdministratorCanGenerateInvitationAndInvalidDurationIsRejected(): void
    {
        [$client, , $planning] = $this->prepare();
        $path = '/dashboard/reservations/plannings/'.$planning->getId().'/invitation';
        $client->request('GET', $path);
        self::assertResponseRedirects();
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');
        $client->loginUser($provider->loadUserByIdentifier('admin@example.test'));
        $crawler = $client->request('GET', $path);
        self::assertResponseIsSuccessful();
        $client->submit($crawler->selectButton('Créer le lien signé')->form(['form[hours]' => 73]));
        self::assertResponseStatusCodeSame(422);
        $crawler = $client->request('GET', $path);
        $crawler = $client->submit($crawler->selectButton('Créer le lien signé')->form(['form[hours]' => 72]));
        self::assertResponseIsSuccessful();
        $url = $crawler->filter('#planning-invitation-url')->attr('value');
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        self::assertTrue(self::getContainer()->get(PlanningInvitationService::class)->canAccess($planning, $query['access']));
    }

    public function testMissingAndPrivatePlanningBlocksDoNotExposeABookingLink(): void
    {
        [, $em, $planning] = $this->prepare();
        $block = self::getContainer()->get(PlanningBlock::class);
        $block->data = new PlanningBlockDTO();
        self::assertNull($block->getPlanning());
        $block->data->planningId = 999;
        self::assertNull($block->getPlanning());
        $block->data->planningId = $planning->getId();
        self::assertNull($block->getPlanning());
        $planning->setIsOnline(true);
        $em->flush();
        self::assertSame($planning, $block->getPlanning());
        $planning->archive();
        $em->flush();
        self::assertNull($block->getPlanning());
    }

    /** @return array{KernelBrowser, EntityManagerInterface, Planning} */
    private function prepare(string $timezone = 'Europe/Paris'): array
    {
        $client = self::createClient();
        $client->disableReboot();
        $settings = new GeneralSettingsDto();
        $settings->timezone = $timezone;
        $cache = new ArrayAdapter();
        $cache->get(GeneralSettingsCache::KEY, static fn (): GeneralSettingsDto => $settings);
        self::getContainer()->set(GeneralSettingsCache::class, new GeneralSettingsCache($cache));
        self::getContainer()->set('doctrine.dbal.default_connection', DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]));
        $em = self::getContainer()->get(EntityManagerInterface::class);
        (new SchemaTool($em))->createSchema($em->getMetadataFactory()->getAllMetadata());
        $planning = (new Planning())->setTitle('Privé')->setSlug('private')->setDuration(30)->setGap(10)->setIsActive(true)->setIsOnline(false);
        $availability = (new Availability())->setDow((int) (new \DateTimeImmutable('tomorrow', new \DateTimeZone($timezone)))->format('N'))->setStartHour(new \DateTimeImmutable('09:00'))->setEndHour(new \DateTimeImmutable('12:00'));
        $planning->addAvailability($availability);
        $em->persist($planning);
        $em->persist($availability);
        $em->flush();

        return [$client, $em, $planning];
    }
}
