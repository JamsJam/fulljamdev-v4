<?php

namespace App\Application\Shared\Notification;

use Symfony\Component\Notifier\Notification\Notification;

final class LogNotification extends Notification
{
    /** @param array<string, mixed> $context */
    public function __construct(string $subject, private readonly array $context)
    {
        parent::__construct($subject, ['log']);
        $this->importance(self::IMPORTANCE_HIGH);
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return $this->context;
    }
}
