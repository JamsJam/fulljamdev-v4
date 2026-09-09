<?php

namespace App\Tests\Security\Application;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class SecurityAccessTest extends WebTestCase
{
    public function testLoginPageIsPublicAndContainsExpectedControls(): void
    {
        $client = self::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="_username"][type="email"]');
        self::assertSelectorExists('input[name="_password"][type="password"]');
        self::assertSelectorExists('input[name="_remember_me"][type="checkbox"]');
        self::assertSelectorExists('input[name="_csrf_token"][data-controller="csrf-protection"]');
        self::assertSelectorExists('[data-controller="password-visibility"]');
    }

    public function testAnonymousVisitorIsRedirectedToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/dashboard/');

        self::assertResponseRedirects('http://localhost/login');
    }

    public function testAdminCanAuthenticateWithEmailPasswordAndRememberMe(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin@example.test',
            '_password' => 'AdminPassword123!',
            '_remember_me' => true,
        ]);

        $client->submit($form);

        self::assertResponseRedirects('http://localhost/dashboard/');
        self::assertNotNull($client->getCookieJar()->get('REMEMBERME'));
    }

    public function testAuthenticationFailsWithInvalidCredentials(): void
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin@example.test',
            '_password' => 'incorrect-password',
        ]);

        $client->submit($form);

        self::assertResponseRedirects('http://localhost/login');
        $client->followRedirect();
        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.login__error[role="alert"]');

        $client->request('GET', '/dashboard/');
        self::assertResponseRedirects('http://localhost/login');
    }

    public function testRegularUserCannotAccessDashboard(): void
    {
        $client = self::createClient();
        $client->loginUser($this->user('user@example.test'));
        $client->request('GET', '/dashboard/');

        self::assertResponseStatusCodeSame(403);
    }

    public function testAdminRoleIncludesUserRole(): void
    {
        self::bootKernel();
        $hierarchy = self::getContainer()->get(RoleHierarchyInterface::class);

        self::assertContains('ROLE_USER', $hierarchy->getReachableRoleNames(['ROLE_ADMIN']));
    }

    public function testEveryDashboardRouteRejectsAnAnonymousVisitor(): void
    {
        self::bootKernel();
        $this->assertDashboardDecision(new NullToken(), false);
    }

    public function testEveryDashboardRouteRejectsARegularUser(): void
    {
        self::bootKernel();
        $user = $this->user('user@example.test');

        $this->assertDashboardDecision(new UsernamePasswordToken($user, 'main', $user->getRoles()), false);
    }

    public function testEveryDashboardRouteAllowsAnAdministrator(): void
    {
        self::bootKernel();
        $admin = $this->user('admin@example.test');

        $this->assertDashboardDecision(new UsernamePasswordToken($admin, 'main', $admin->getRoles()), true);
    }

    public function testEveryDashboardControllerHasAnAdminAttribute(): void
    {
        self::bootKernel();
        $routes = self::getContainer()->get(RouterInterface::class)->getRouteCollection();
        $controllers = [];

        foreach ($routes as $route) {
            if (!str_starts_with($route->getPath(), '/dashboard')) {
                continue;
            }

            $controller = $route->getDefault('_controller');
            self::assertIsString($controller);
            $controllerClass = explode('::', $controller)[0];
            $controllers[$controllerClass] = true;
        }

        self::assertNotEmpty($controllers);
        foreach (array_keys($controllers) as $controllerClass) {
            $attributes = (new \ReflectionClass($controllerClass))->getAttributes(IsGranted::class);
            self::assertNotEmpty($attributes, sprintf('%s doit porter #[IsGranted].', $controllerClass));
            self::assertSame('ROLE_ADMIN', $attributes[0]->newInstance()->attribute);
        }
    }

    private function assertDashboardDecision(TokenInterface $token, bool $expected): void
    {
        $container = self::getContainer();
        $routes = $container->get(RouterInterface::class)->getRouteCollection();
        $accessMap = $container->get('security.access_map');
        $decisionManager = $container->get(AccessDecisionManagerInterface::class);
        $testedRoutes = 0;

        foreach ($routes as $route) {
            if (!str_starts_with($route->getPath(), '/dashboard')) {
                continue;
            }

            [$attributes] = $accessMap->getPatterns(Request::create($route->getPath()));
            self::assertSame(['ROLE_ADMIN'], $attributes, sprintf('Protection incorrecte pour %s.', $route->getPath()));
            self::assertSame($expected, $decisionManager->decide($token, $attributes));
            ++$testedRoutes;
        }

        self::assertGreaterThan(0, $testedRoutes);
    }

    private function user(string $identifier): UserInterface
    {
        /** @var UserProviderInterface $provider */
        $provider = self::getContainer()->get('security.user.provider.concrete.test_user_provider');

        return $provider->loadUserByIdentifier($identifier);
    }
}
