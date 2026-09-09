<?php

namespace App\EventSubscriber;

use App\Application\Settings\Service\GetGeneralSettingsService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

final readonly class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private GetGeneralSettingsService $settings,
        private TokenStorageInterface $tokenStorage,
        private Environment $twig,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // After routing (32) and the security firewall (8), before controllers.
        return [KernelEvents::REQUEST => ['onKernelRequest', -1]];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || $this->isExcluded($event->getRequest())) {
            return;
        }

        $settings = $this->settings->get();
        if (!$settings->maintenanceEnabled || null !== $this->tokenStorage->getToken()?->getUser()) {
            return;
        }

        $event->setResponse(new Response(
            $this->twig->render('front/maintenance.html.twig', ['settings' => $settings]),
            Response::HTTP_SERVICE_UNAVAILABLE,
            ['Cache-Control' => 'no-store, private', 'X-Robots-Tag' => 'noindex, nofollow'],
        ));
    }

    private function isExcluded(Request $request): bool
    {
        return in_array($request->attributes->get('_route'), ['app_login', 'app_logout'], true)
            || 1 === preg_match('#^/(?:dashboard|assets|_profiler|_wdt|_error)(?:/|$)#', $request->getPathInfo());
    }
}
