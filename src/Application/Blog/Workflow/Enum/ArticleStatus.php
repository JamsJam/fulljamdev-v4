<?php

namespace App\Application\Blog\Workflow\Enum;

enum ArticleStatus: string
{
    case DRAFT = 'draft';
    case REVIEW = 'review';
    case SCHEDULED = 'scheduled';
    case PUBLISHED = 'published';
}
