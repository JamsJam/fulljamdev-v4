<?php

namespace App\Twig\Components\Front;

use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Front:Pagination', template: 'components/front/Pagination.html.twig')]
final class Pagination
{
    /** @var PaginationInterface<int, mixed> */
    public PaginationInterface $pagination;

    /** @return list<int|string> */
    public function getPages(): array
    {
        $last = $this->getPageCount();
        $current = $this->pagination->getCurrentPageNumber();
        if ($last <= 7) {
            return range(1, $last);
        }

        $pages = [1];
        $start = max(2, $current - 1);
        $end = min($last - 1, $current + 1);
        if ($start > 2) {
            $pages[] = 'start-ellipsis';
        }
        for ($page = $start; $page <= $end; ++$page) {
            $pages[] = $page;
        }
        if ($end < $last - 1) {
            $pages[] = 'end-ellipsis';
        }
        $pages[] = $last;

        return $pages;
    }

    public function getPageCount(): int
    {
        return (int) ceil($this->pagination->getTotalItemCount() / $this->pagination->getItemNumberPerPage());
    }
}
