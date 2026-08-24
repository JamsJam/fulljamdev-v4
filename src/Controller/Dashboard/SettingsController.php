<?php

namespace App\Controller\Dashboard;

use App\Application\Page\Page\Service\GetPagesService;
use App\Application\Settings\Service\GetAccountSettingsService;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Application\Settings\Service\UpdateAccountSettingsService;
use App\Application\Settings\Service\UpdateGeneralSettingsService;
use App\Entity\Page\Page;
use App\Form\AccountSettingsType;
use App\Form\GeneralSettingsType;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsController extends AbstractController
{
    private const SECTION_TEMPLATES = [
        'general' => 'dashboard/settings/sections/general.html.twig',
        'reservation' => 'dashboard/settings/sections/reservation.html.twig',
        'pages' => 'dashboard/settings/sections/pages.html.twig',
        'account' => 'dashboard/settings/sections/account.html.twig',
    ];

    #[Route(
        '/dashboard/settings/{section}',
        name: 'app_dashboard_settings',
        requirements: ['section' => 'general|reservation|pages|account'],
        defaults: ['section' => 'general'],
        methods: ['GET', 'POST'],
    )]
    public function index(
        string $section,
        Request $request,
        BreadcrumbService $breadcrumbService,
        GetPagesService $getPagesService,
        GetGeneralSettingsService $getGeneralSettingsService,
        UpdateGeneralSettingsService $updateGeneralSettingsService,
        GetAccountSettingsService $getAccountSettingsService,
        UpdateAccountSettingsService $updateAccountSettingsService,
    ): Response {
        $pages = $getPagesService->get();
        $settings = $getGeneralSettingsService->get();
        $form = $this->createForm(GeneralSettingsType::class, $settings, [
            'action' => $this->generateUrl('app_dashboard_settings', ['section' => 'general']),
            'page_choices' => array_combine(
                array_map(static fn (Page $page): string => sprintf('%s (/%s)', $page->getTitle(), $page->getPath()), $pages),
                array_map(static fn (Page $page): int => (int) $page->getId(), $pages),
            ),
        ]);
        $account = $getAccountSettingsService->get();
        $accountForm = $this->createForm(AccountSettingsType::class, $account, [
            'action' => $this->generateUrl('app_dashboard_settings', ['section' => 'account']),
        ]);

        if ('general' === $section) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $updateGeneralSettingsService->update($settings);
                    $this->addFlash('success', 'Les paramètres généraux ont été enregistrés.');

                    return $this->redirectToRoute('app_dashboard_settings', ['section' => 'general']);
                } catch (\RuntimeException|\DomainException $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
            }
        } elseif ('account' === $section) {
            $accountForm->handleRequest($request);
            if ($accountForm->isSubmitted() && $accountForm->isValid()) {
                try {
                    $updateAccountSettingsService->update($account);
                    $this->addFlash('success', 'Les informations du compte ont été enregistrées.');

                    return $this->redirectToRoute('app_dashboard_settings', ['section' => 'account']);
                } catch (\RuntimeException $exception) {
                    $accountForm->addError(new FormError($exception->getMessage()));
                }
            }
        }

        return $this->render('dashboard/settings/index.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
            'active_section' => $section,
            'section_template' => self::SECTION_TEMPLATES[$section],
            'general_form' => $form,
            'account_form' => $accountForm,
            'pages' => 'pages' === $section ? $pages : [],
        ]);
    }
}
