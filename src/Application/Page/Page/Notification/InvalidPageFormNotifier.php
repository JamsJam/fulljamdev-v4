<?php

namespace App\Application\Page\Page\Notification;

use App\Application\Shared\Notification\LogNotification;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\NotifierInterface;

final readonly class InvalidPageFormNotifier
{
    public function __construct(private NotifierInterface $notifier)
    {
    }

    public function notify(FormInterface $form, Request $request, ?int $pageId): void
    {
        $diagnostics = [];
        $this->collectDiagnostics($form, $form->getName(), $diagnostics);

        $this->notifier->send(new LogNotification('page_builder.form_invalid', [
            'page_id' => $pageId,
            'route' => $request->attributes->getString('_route'),
            'blocks' => $this->blockMetadata($request, $form->getName()),
            'diagnostics' => $diagnostics,
        ]));
    }

    /** @param list<array<string, mixed>> $diagnostics */
    private function collectDiagnostics(FormInterface $form, string $path, array &$diagnostics): void
    {
        $failure = $form->getTransformationFailure();
        if (null !== $failure) {
            $causes = [];
            for ($cause = $failure; null !== $cause; $cause = $cause->getPrevious()) {
                $causes[] = ['class' => $cause::class, 'message' => $cause->getMessage()];
            }
            $diagnostics[] = ['path' => $path, 'kind' => 'transformation', 'causes' => $causes];
        }

        /** @var FormError $error */
        foreach ($form->getErrors(false) as $error) {
            $cause = $error->getCause();
            $diagnostics[] = [
                'path' => $path,
                'kind' => 'form_error',
                'message' => $error->getMessage(),
                'cause_class' => is_object($cause) ? $cause::class : null,
            ];
        }

        foreach ($form as $name => $child) {
            $this->collectDiagnostics($child, $path.'.'.$name, $diagnostics);
        }
    }

    /** @return list<array{index: int|string, id: int|string|null, type: string|null}> */
    private function blockMetadata(Request $request, string $formName): array
    {
        $payload = $request->request->all();
        $submitted = $payload[$formName] ?? [];
        $blocks = is_array($submitted) ? ($submitted['blocks'] ?? []) : [];
        if (!is_array($blocks)) {
            return [];
        }

        $metadata = [];
        foreach ($blocks as $index => $block) {
            $metadata[] = [
                'index' => $index,
                'id' => is_array($block) && is_scalar($block['id'] ?? null) ? $block['id'] : null,
                'type' => is_array($block) && is_string($block['type'] ?? null) ? $block['type'] : null,
            ];
        }

        return $metadata;
    }
}
