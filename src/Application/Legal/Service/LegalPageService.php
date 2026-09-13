<?php

namespace App\Application\Legal\Service;

use App\Application\Legal\Dto\LegalPageDTO;
use App\Application\Legal\Dto\LegalSettingsDTO;
use App\Entity\Legal\LegalPage;
use App\Repository\Legal\LegalPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

final readonly class LegalPageService
{
    private const PAGES = [
        'legalNotice' => ['mention-legal', 'Mentions légales'],
        'privacyPolicy' => ['politique-confidentialite', 'Politique de confidentialité'],
        'cookiePolicy' => ['polotique-cookies', 'Politique relative aux cookies'],
    ];

    public function __construct(
        private LegalPageRepository $repository,
        private EntityManagerInterface $entityManager,
        #[Target('app.legal_sanitizer')] private HtmlSanitizerInterface $sanitizer,
    ) {
    }

    public function get(string $slug): ?LegalPage
    {
        return $this->repository->findOneBy(['slug' => $slug]);
    }

    public function settings(): LegalSettingsDTO
    {
        $settings = new LegalSettingsDTO();
        foreach (self::PAGES as $property => [$slug, $defaultTitle]) {
            $page = $this->get($slug);
            $dto = new LegalPageDTO();
            $dto->title = $page?->getTitle() ?: $defaultTitle;
            $dto->content = $page?->getContent() ?: '<p>À rédiger.</p>';
            $settings->{$property} = $dto;
        }

        return $settings;
    }

    public function save(LegalSettingsDTO $settings): void
    {
        foreach (self::PAGES as $property => [$slug, $defaultTitle]) {
            $dto = $settings->{$property};
            $page = $this->get($slug) ?? (new LegalPage())->setSlug($slug);
            $page->setTitle($dto->title ?: $defaultTitle)->setContent($this->sanitizer->sanitize($dto->content));
            $this->entityManager->persist($page);
        }
        $this->entityManager->flush();
    }
}
