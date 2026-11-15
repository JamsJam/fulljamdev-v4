<?php

namespace App\Application\QrVisitCard\Service;

use App\Application\Page\Page\Provider\PageProvider;
use App\Application\QrVisitCard\Persister\QrVisitCardPersister;
use App\Application\QrVisitCard\Provider\QrVisitCardProvider;
use App\Application\QrVisitCard\Proxy\QrVisitCardRouteProxy;
use App\Entity\QrVisitCard\QrVisitCard;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

final readonly class QrVisitCardRouteService
{
    public function __construct(
        private QrVisitCardProvider $provider,
        private QrVisitCardPersister $persister,
        private QrVisitCardRouteProxy $proxy,
        private UrlMatcherInterface $urlMatcher,
        private PageProvider $pageProvider,
    ) {
    }

    /** Enregistre le chemin de destination, par exemple « /contact ». */
    public function write(?string $path): void
    {
        $path = $this->normalizePath($path);
        if (null !== $path && !$this->routeExists($path)) {
            throw new \DomainException(sprintf('La route « %s » n’existe pas.', $path));
        }

        $qrVisitCard = $this->provider->provide() ?? new QrVisitCard();
        $qrVisitCard->setPath($path);
        $this->persister->persist($qrVisitCard);
        $this->proxy->invalidate();
    }

    /** Retourne un chemin de route existant, ou la page d’accueil en l’absence de cible valide. */
    public function read(): string
    {
        return $this->proxy->get(fn (): string => $this->resolveRoute());
    }

    private function resolveRoute(): string
    {
        $path = $this->normalizePath($this->provider->provide()?->getPath());
        if (null === $path || !$this->routeExists($path)) {
            return '/';
        }

        return $path;
    }

    private function routeExists(string $path): bool
    {
        if ('/qrvisitcard' === rtrim($path, '/')) {
            return false;
        }

        try {
            $matchedRoute = $this->urlMatcher->match($path);
            if ('app_front_page' === ($matchedRoute['_route'] ?? null)) {
                return null !== $this->pageProvider->provideOneByPath((string) ($matchedRoute['path'] ?? ''));
            }

            return true;
        } catch (ResourceNotFoundException|MethodNotAllowedException) {
            return false;
        }
    }

    private function normalizePath(?string $path): ?string
    {
        if (null === $path) {
            return null;
        }

        $path = trim($path);
        if ('' === $path || !str_starts_with($path, '/') || str_starts_with($path, '//')
            || str_contains($path, "\0") || preg_match('/^[a-z][a-z0-9+.-]*:/i', $path)) {
            return null;
        }

        return $path;
    }
}
