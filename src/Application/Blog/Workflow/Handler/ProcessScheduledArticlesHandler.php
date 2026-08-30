<?php

namespace App\Application\Blog\Workflow\Handler;

use App\Application\Blog\Workflow\Message\ProcessScheduledArticles;
use App\Application\Blog\Workflow\Service\ProcessScheduledArticlesService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProcessScheduledArticlesHandler
{
    public function __construct(private ProcessScheduledArticlesService $processor)
    {
    }

    public function __invoke(ProcessScheduledArticles $message): void
    {
        $this->processor->process();
    }
}
