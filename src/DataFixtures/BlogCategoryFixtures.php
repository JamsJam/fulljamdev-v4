<?php

namespace App\DataFixtures;

use App\Entity\Blog\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

final class BlogCategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public const DEVELOPMENT = 'blog.category.development';
    public const DESIGN = 'blog.category.design';
    public const PERFORMANCE = 'blog.category.performance';
    public const BUSINESS = 'blog.category.business';
    public const SECURITY = 'blog.category.security';

    private const CATEGORIES = [
        self::DEVELOPMENT => [
            'name' => 'Développement web',
            'slug' => 'developpement-web',
            'description' => 'Architecture, qualité du code et développement d’applications web.',
        ],
        self::DESIGN => [
            'name' => 'Design UX/UI',
            'slug' => 'design-ux-ui',
            'description' => 'Conception d’interfaces accessibles, cohérentes et agréables à utiliser.',
        ],
        self::PERFORMANCE => [
            'name' => 'Performance',
            'slug' => 'performance',
            'description' => 'Optimisation des performances techniques et de l’expérience utilisateur.',
        ],
        self::BUSINESS => [
            'name' => 'Conseils',
            'slug' => 'conseils',
            'description' => 'Méthodes et conseils pour cadrer et faire progresser un projet numérique.',
        ],
        self::SECURITY => [
            'name' => 'Sécurité web',
            'slug' => 'securite-web',
            'description' => 'Bonnes pratiques pour protéger les applications, les données et leurs utilisateurs.',
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $repository = $manager->getRepository(Category::class);

        foreach (self::CATEGORIES as $reference => $data) {
            $category = $repository->findOneBy(['slug' => $data['slug']]) ?? new Category();
            $category
                ->setName($data['name'])
                ->setSlug($data['slug'])
                ->setDescription($data['description']);

            $manager->persist($category);
            $this->addReference($reference, $category);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['blog'];
    }
}
