<?php

namespace App\Tests\Blog\Application;

use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class BlogDashboardTest extends WebTestCase
{
    #[DataProvider('dashboardPages')]
    public function testDashboardPageIsRendered(string $path, string $heading): void
    {
        $client = self::createClient();
        $client->loginUser($this->admin());
        $client->request('GET', $path);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $heading);
    }

    /** @return iterable<string, array{string, string}> */
    public static function dashboardPages(): iterable
    {
        yield 'blog' => ['/dashboard/blog', 'Blog'];
        yield 'nouvel article' => ['/dashboard/blog/article/new', 'Ajouter un article'];
        yield 'nouvelle catégorie' => ['/dashboard/blog/category/new', 'Ajouter une catégorie'];
    }

    private function admin(): UserInterface
    {
        /** @var UserProviderInterface $provider */
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');

        return $provider->loadUserByIdentifier('admin@example.test');
    }
}
