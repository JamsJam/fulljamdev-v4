<?php

namespace App\Tests\Page\Integration\Notification;

use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Form\PageType;
use App\Application\Page\Page\Notification\InvalidPageFormNotifier;
use App\Application\Shared\Notification\LogNotification;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\NoRecipient;

final class InvalidPageFormNotifierTest extends KernelTestCase
{
    public function testItSendsSanitizedDiagnosticsThroughTheLogChannel(): void
    {
        self::bootKernel();
        $form = self::getContainer()->get(FormFactoryInterface::class)->create(PageType::class, new PageDTO(), ['csrf_protection' => false]);
        $payload = ['title' => ['secret-page-content'], 'path' => 'accueil', 'seo' => [], 'blocks' => []];
        $form->submit($payload);
        self::assertFalse($form->isValid());

        $notifier = $this->createMock(NotifierInterface::class);
        $notifier->expects(self::once())->method('send')->with(self::callback(static function (LogNotification $notification): bool {
            self::assertSame(['log'], $notification->getChannels(new NoRecipient()));
            self::assertSame('page.title', $notification->context()['diagnostics'][0]['path']);
            self::assertStringNotContainsString('secret-page-content', serialize($notification->context()));

            return true;
        }));
        $request = Request::create('/dashboard/settings/pages/1/edit', 'POST', ['page' => $payload]);
        $request->attributes->set('_route', 'app_dashboard_page_edit');

        (new InvalidPageFormNotifier($notifier))->notify($form, $request, 1);
    }
}
