<?php

namespace App\DataFixtures;

use App\Entity\Blog\Post;
use App\Entity\Blog\BlogAuthor;
use App\Entity\Blog\BlogCategory;
use App\Entity\Blog\BlogTags;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // --- Categories ---
        $categories = [];
        for ($i = 0; $i < 3; $i++) {
            $category = new BlogCategory();
            $category->setName($faker->unique()->word())
                ->setDescription($faker->sentence(10))
                ->setIntroduction($faker->paragraph(3))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setEditedAt(new \DateTimeImmutable());

            $manager->persist($category);
            $categories[] = $category;
        }

        // --- Authors ---
        $authors = [];
        for ($i = 0; $i < 5; $i++) {
            $author = new BlogAuthor();
            $author->setName($faker->lastName())
                ->setFistname($faker->firstName())
                ->setDescription($faker->sentence(8))
                ->setLink($faker->optional()->url());

            $manager->persist($author);
            $authors[] = $author;
        }

        // --- Tags ---
        $tags = [];
        for ($i = 0; $i < 8; $i++) {
            $tag = new BlogTags();
            $tag->setName($faker->unique()->word())
                ->setDescription($faker->optional()->sentence(8))
                ->setCreatedAt(new \DateTimeImmutable())
                ->setEditedAt(new \DateTimeImmutable());

            $manager->persist($tag);
            $tags[] = $tag;
        }

        // --- Posts ---
        for ($i = 0; $i < 10; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence(5))
                ->setDescription($faker->sentence(15))
                ->setContent($faker->paragraphs(5, true))
                ->setImage($faker->optional()->imageUrl(640, 480, 'business', true))
                ->setSlug($faker->slug())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setEditedAt(new \DateTimeImmutable())
                ->setPublishedAt((new \DateTimeImmutable())->createFromInterface($faker->optional()->dateTimeBetween('-1 years', 'now') ))
                ->setStatus($faker->numberBetween(0, 3))
                ->setCategory($faker->randomElement($categories))
                ->setAuthor($faker->randomElement($authors));

            // Ajout de tags aléatoires
            foreach ($faker->randomElements($tags, rand(1, 3)) as $tag) {
                $post->addTag($tag);
            }

            // Ajout de co-auteurs aléatoires
            foreach ($faker->randomElements($authors, rand(0, 2)) as $coAuthor) {
                if ($coAuthor !== $post->getAuthor()) {
                    $post->addCollaborator($coAuthor);
                }
            }

            $manager->persist($post);
        }

        $manager->flush();
    }
}
