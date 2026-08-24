<?php

namespace App\EventSubscriber;

use App\Application\Page\Page\Service\HomepageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class HomepageRedirectSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private HomepageService $homepageService,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => 'redirectHomepagePath'];
    }

    public function redirectHomepagePath(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || Response::HTTP_OK !== $event->getResponse()->getStatusCode()) {
            return;
        }

        $request = $event->getRequest();
        if ('app_front_page' !== $request->attributes->getString('_route')) {
            return;
        }

        $path = $request->attributes->getString('path');
        if (!$this->homepageService->hasPath($path)) {
            return;
        }

        $event->setResponse(new RedirectResponse(
            $this->urlGenerator->generate('app_home'),
            Response::HTTP_MOVED_PERMANENTLY,
        ));
    }
}
