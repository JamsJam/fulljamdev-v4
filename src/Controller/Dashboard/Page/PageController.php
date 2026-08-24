<?php

namespace App\Controller\Dashboard\Page;

use App\Application\Page\Block\Registry\BlockRegistry;
use App\Application\Page\Page\Builder\PageBuilder;
use App\Application\Page\Page\Dto\PageBlockDTO;
use App\Application\Page\Page\Dto\PageDTO;
use App\Application\Page\Page\Form\PageType;
use App\Application\Page\Page\Service\CheckPagePathAvailabilityService;
use App\Application\Page\Page\Service\FindPageService;
use App\Application\Page\Page\Service\SavePageService;
use App\Entity\Page\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/settings/pages')]
final class PageController extends AbstractController
{
    #[Route('/new', name: 'app_dashboard_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SavePageService $saveService, BlockRegistry $registry, CheckPagePathAvailabilityService $pathService): Response
    {
        return $this->editForm($request, new PageDTO(), null, $saveService, $registry, $pathService);
    }

    #[Route('/{id}/edit', name: 'app_dashboard_page_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, PageBuilder $builder, SavePageService $saveService, BlockRegistry $registry, FindPageService $findService, CheckPagePathAvailabilityService $pathService): Response
    {
        $page = $findService->find($id) ?? throw $this->createNotFoundException('Cette page n’existe pas.');

        return $this->editForm($request, $builder->build($page), $page, $saveService, $registry, $pathService);
    }

    #[Route('/blocks/new/{type}/{index}', name: 'app_dashboard_page_block_new', requirements: ['type' => '[a-z0-9.-]+', 'index' => '\d+'], methods: ['GET'])]
    public function newBlock(string $type, int $index, BlockRegistry $registry, FormFactoryInterface $forms): Response
    {
        $definition = $registry->get($type);
        $dto = new PageDTO();
        $dto->blocks[$index] = new PageBlockDTO(null, $type, $definition->createDefaultData());
        $form = $forms->create(PageType::class, $dto);

        return $this->render('dashboard/page/_block_form.html.twig', [
            'block' => $form->get('blocks')->get((string) $index)->createView(),
            'definition' => $definition,
        ]);
    }

    private function editForm(Request $request, PageDTO $dto, ?Page $page, SavePageService $saveService, BlockRegistry $registry, CheckPagePathAvailabilityService $pathService): Response
    {
        $form = $this->createForm(PageType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($pathService->isUsedByAnotherPage($dto->path, $page)) {
                $form->get('path')->addError(new \Symfony\Component\Form\FormError('Ce chemin est déjà utilisé par une autre page.'));
            } elseif ($pathService->conflictsWithApplicationRoute($dto->path)) {
                $form->get('path')->addError(new \Symfony\Component\Form\FormError('Ce chemin est réservé par une route de l’application.'));
            } else {
                $page = $saveService->save($dto, $page);
                $this->addFlash('success', 'La page a été enregistrée.');

                return $this->redirectToRoute('app_dashboard_page_edit', ['id' => $page->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('dashboard/page/edit.html.twig', [
            'form' => $form,
            'page' => $dto,
            'block_groups' => $registry->grouped(),
            'definitions' => $registry->all(),
        ], new Response(status: $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }
}
