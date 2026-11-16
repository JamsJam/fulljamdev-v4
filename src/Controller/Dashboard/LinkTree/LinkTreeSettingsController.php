<?php

namespace App\Controller\Dashboard\LinkTree;

use App\Application\LinkTree\Dto\LinkTreeDto;
use App\Application\LinkTree\Form\LinkTreeType;
use App\Application\LinkTree\Service\GetLinkTreeService;
use App\Application\LinkTree\Service\SaveLinkTreeProfileService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class LinkTreeSettingsController extends AbstractController
{
    #[Route('/dashboard/linktree/settings', name: 'app_dashboard_linktree_settings', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, GetLinkTreeService $get, SaveLinkTreeProfileService $save, BreadcrumbService $breadcrumbs): Response
    {
        $tree = $get->get();
        $dto = new LinkTreeDto();
        $dto->name = $tree->getName();
        $dto->company = $tree->getCompany();
        $form = $this->createForm(LinkTreeType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $save->save($tree, $dto);
            $this->addFlash('success', 'Les paramètres LinkTree ont été enregistrés.');

            return $this->redirectToRoute('app_dashboard_linktree_settings', status: Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard/linktree/settings.html.twig', [
            'tree' => $tree, 'form' => $form->createView(),
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ], new Response(status: $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }
}
