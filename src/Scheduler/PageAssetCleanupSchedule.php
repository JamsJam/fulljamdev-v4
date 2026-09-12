<?php

namespace App\Scheduler;

use App\Application\Page\Block\Asset\Message\CleanupOrphanedPageImages;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('page_assets')]
final readonly class PageAssetCleanupSchedule implements ScheduleProviderInterface
{
    public function __construct(private CacheInterface $cache)
    {
    }

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->stateful($this->cache)
            ->processOnlyLastMissedRun(true)
            ->add(RecurringMessage::every(
                '1 day',
                new CleanupOrphanedPageImages(),
                '2020-01-01 03:00:00 Europe/Paris',
            ))
        ;
    }
}
