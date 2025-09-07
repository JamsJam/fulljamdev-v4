<?php

namespace App\Application\Blog\Post\Provider;

final class PostProvider
{
    public function __construct()
    {
        // private PostRepository $postRepository
    }

    // public function findByCategory(Category $category)
    // public function findByTag(Tag $tag)
    // public function findByStatus(Tag $tag)

    public function searchAndSortPost(?string $query = null, ?string $orderBy = null, ?string $orderDirection = null): array
    {
        // $posts =  $this->postRepository->searchAndSort($query, $orderBy, $orderDirection);
        return $posts = [];
    }
}
