<?php
namespace App\Application\Blog\Services;

use App\Application\Blog\Post\Provider\PostProvider;

final class BlogService
{
    public function __construct(
        private PostProvider $postProvider
    ) {}
    //?===== Selection

    public function getPosts()
    {
        $posts = [];

        return $posts;
    }

    public function getPostOrderBy(?string $query = null, ?string $orderBy = null, ?string $orderDirection = null ) : array
    {
        // $posts = $this->postProvider->searchAndSortPost($query, $orderBy, $orderDirection);
        $posts = [];

        return $posts;
    }

    //?===== Creation
    
    public function createPostFrom()
    {}


    //?===== Edition
    
    public function editPostFrom()
    {}

    public function editPostFromm()
    {}
}
