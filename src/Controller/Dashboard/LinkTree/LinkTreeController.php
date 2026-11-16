<?php

namespace App\Controller\Dashboard\LinkTree;

use App\Application\LinkTree\Dto\LinkDto;
use App\Application\LinkTree\Form\AddLinkType;
use App\Application\LinkTree\Form\LinkType;
use App\Application\LinkTree\Service\DeleteLinkService;
use App\Application\LinkTree\Service\GetLinkTreeService;
use App\Application\LinkTree\Service\SaveLinkService;
use App\Service\Breadcrumb\BreadcrumbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class LinkTreeController extends AbstractController
{
    #[Route('/dashboard/linktree', name: 'app_dashboard_linktree', methods: ['GET', 'POST'])]
    public function index(Request $request, GetLinkTreeService $get, SaveLinkService $save, BreadcrumbService $breadcrumbs): Response
    {
        $tree = $get->get();
        $dto = new LinkDto();
        $addForm = $this->createForm(AddLinkType::class, $dto, ['action' => $this->generateUrl('app_dashboard_linktree')]);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            try {
                $save->add($tree, $dto);
                $this->addFlash('success', 'Le lien a été ajouté.');

                return $this->redirectToRoute('app_dashboard_linktree', status: Response::HTTP_SEE_OTHER);
            } catch (\DomainException $exception) {
                $addForm->addError(new \Symfony\Component\Form\FormError($exception->getMessage()));
            }
        }
        $linkForms = [];
        foreach ($tree->getLinks() as $link) {
            $linkForms[$link->getId()] = $this->createForm(LinkType::class, LinkDto::fromLink($link), [
                'action' => $this->generateUrl('app_dashboard_linktree_link_edit', ['id' => $link->getId()]),
            ])->createView();
        }

        return $this->render('dashboard/linktree/index.html.twig', [
            'tree' => $tree, 'add_form' => $addForm->createView(), 'link_forms' => $linkForms,
            'breadcrumb' => $breadcrumbs->getBreadcrumb($request->attributes->getString('_route')),
        ], new Response(status: $addForm->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }

    #[Route('/dashboard/linktree/link/{id}/edit', name: 'app_dashboard_linktree_link_edit', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function edit(int $id, Request $request, SaveLinkService $save): Response
    {
        $form = $this->createForm(LinkType::class, new LinkDto());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $save->update($id, $form->getData());
                $this->addFlash('success', 'Le lien a été mis à jour.');
            } catch (\DomainException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('app_dashboard_linktree', status: Response::HTTP_SEE_OTHER);
    }

    #[Route('/dashboard/linktree/link/{id}/delete', name: 'app_dashboard_linktree_link_delete', requirements: ['id' => '\\d+'], methods: ['POST'])]
    public function delete(int $id, Request $request, DeleteLinkService $delete): Response
    {
        if ($this->isCsrfTokenValid('delete_link_'.$id, $request->request->getString('_token'))) {
            $delete->delete($id);
            $this->addFlash('success', 'Le lien a été supprimé.');
        }

        return $this->redirectToRoute('app_dashboard_linktree', status: Response::HTTP_SEE_OTHER);
    }
}
