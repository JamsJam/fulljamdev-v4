<?php

namespace App\Controller\Dashboard;

use App\Application\Settings\Dto\AccountSettingsDto;
use App\Application\Settings\Dto\GeneralSettingsDto;
use App\Form\AccountSettingsType;
use App\Form\GeneralSettingsType;
use App\Service\Breadcrumb\BreadcrumbService;
use App\Service\ConfigurationService;
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
        'account' => 'dashboard/settings/sections/account.html.twig',
    ];

    #[Route(
        '/dashboard/settings/{section}',
        name: 'app_dashboard_settings',
        requirements: ['section' => 'general|reservation|account'],
        defaults: ['section' => 'general'],
        methods: ['GET', 'POST'],
    )]
    public function index(
        string $section,
        Request $request,
        BreadcrumbService $breadcrumbService,
        ConfigurationService $configuration,
    ): Response {
        $settings = new GeneralSettingsDto();
        $settings->timezone = (string) $configuration->get('parameters.timezone', 'Europe/Paris');
        $form = $this->createForm(GeneralSettingsType::class, $settings, [
            'action' => $this->generateUrl('app_dashboard_settings', ['section' => 'general']),
        ]);
        $account = new AccountSettingsDto();
        $account->firstName = (string) $configuration->get('account.first_name', '');
        $account->lastName = (string) $configuration->get('account.last_name', '');
        $account->email = (string) $configuration->get('account.email', '');
        $account->phoneNumber = (string) $configuration->get('account.phone_number', '');
        $account->company = (string) $configuration->get('account.company', '');
        $account->jobTitle = (string) $configuration->get('account.job_title', '');
        $accountForm = $this->createForm(AccountSettingsType::class, $account, [
            'action' => $this->generateUrl('app_dashboard_settings', ['section' => 'account']),
        ]);

        if ('general' === $section) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $configuration->set('parameters.timezone', $settings->timezone);
                    $this->addFlash('success', 'Les paramètres généraux ont été enregistrés.');

                    return $this->redirectToRoute('app_dashboard_settings', ['section' => 'general']);
                } catch (\RuntimeException $exception) {
                    $form->addError(new FormError($exception->getMessage()));
                }
            }
        } elseif ('account' === $section) {
            $accountForm->handleRequest($request);
            if ($accountForm->isSubmitted() && $accountForm->isValid()) {
                try {
                    $configuration->set('account', [
                        'first_name' => $account->firstName,
                        'last_name' => $account->lastName,
                        'email' => $account->email,
                        'phone_number' => $account->phoneNumber,
                        'company' => $account->company,
                        'job_title' => $account->jobTitle,
                    ]);
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
        ]);
    }
}
