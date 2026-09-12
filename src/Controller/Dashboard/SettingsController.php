<?php

namespace App\Controller\Dashboard;

use App\Application\Settings\Account\Dto\UserAccountDto;
use App\Application\Settings\Account\Service\UpdateUserAccountService;
use App\Application\Settings\Service\GetGeneralSettingsService;
use App\Application\Settings\Service\UpdateGeneralSettingsService;
use App\Entity\User;
use App\Form\GeneralSettingsType;
use App\Form\UserAccountType;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
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
        GetGeneralSettingsService $getGeneralSettingsService,
        UpdateGeneralSettingsService $updateGeneralSettingsService,
        UpdateUserAccountService $updateUserAccountService,
    ): Response {
        $settings = $getGeneralSettingsService->get();
        $form = $this->createForm(GeneralSettingsType::class, $settings, [
            'action' => $this->generateUrl('app_dashboard_settings', ['section' => 'general']),
        ]);
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $userAccount = UserAccountDto::fromUser($user);
        $userAccountForm = $this->createForm(UserAccountType::class, $userAccount, [
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
            $userAccountForm->handleRequest($request);
            if ($userAccountForm->isSubmitted() && $userAccountForm->isValid()) {
                try {
                    $updateUserAccountService->update($user, $userAccount);
                    $this->addFlash('success', 'Le compte de connexion a été mis à jour.');

                    return $this->redirectToRoute('app_dashboard_settings', ['section' => 'account']);
                } catch (\DomainException $exception) {
                    $userAccountForm->addError(new FormError($exception->getMessage()));
                }
            }
        }

        return $this->render('dashboard/settings/index.html.twig', [
            'breadcrumb' => $breadcrumbService->getBreadcrumb($request->attributes->getString('_route')),
            'active_section' => $section,
            'section_template' => self::SECTION_TEMPLATES[$section],
            'general_form' => $form,
            'user_account_form' => $userAccountForm,
        ]);
    }
}
