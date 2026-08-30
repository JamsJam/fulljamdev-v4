<?php

namespace App\Twig\Components\Front;

use App\Entity\Blog\Article;
use App\Entity\Project\Project;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Front:BlogArticleCard', template: 'components/front/blog/BlogArticleCard.html.twig')]
final class BlogArticleCard
{
    public Article|Project $article;
}
