<?php

namespace App\Application\SEO\JsonLd\Enum;

enum PageType: string
{
    case HOME = 'home';
    case STANDARD = 'standard';
    case BLOG_CATALOG = 'blog_catalog';
    case BLOG_ARTICLE = 'blog_article';
    case PROJECT_CATALOG = 'project_catalog';
    case PROJECT = 'project';
    case PLANNING = 'planning';
}
