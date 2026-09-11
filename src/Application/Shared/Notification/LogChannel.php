<?php

namespace App\Application\Shared\Notification;

use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\Channel\ChannelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

final readonly class LogChannel implements ChannelInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function notify(Notification $notification, RecipientInterface $recipient, ?string $transportName = null): void
    {
        if (!$notification instanceof LogNotification) {
            return;
        }

        $this->logger->error($notification->getSubject(), $notification->context());
    }

    public function supports(Notification $notification, RecipientInterface $recipient): bool
    {
        return $notification instanceof LogNotification;
    }
}
