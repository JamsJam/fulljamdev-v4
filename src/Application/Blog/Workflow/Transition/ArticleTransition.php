<?php

namespace App\Application\Blog\Workflow\Transition;

enum ArticleTransition: string
{
    case SUBMIT = 'submit';
    case REJECT = 'reject';
    case SCHEDULE = 'schedule';
    case PUBLISH = 'publish';
    case UNSCHEDULE = 'unschedule';
    case TAKE_OFFLINE = 'take_offline';
}
