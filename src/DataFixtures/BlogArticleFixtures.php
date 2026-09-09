<?php

namespace App\DataFixtures;

use App\Application\Blog\Workflow\Enum\ArticleStatus;
use App\Entity\Blog\Article;
use App\Entity\Blog\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class BlogArticleFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private const COVER_IMAGE = 'images/about-me.webp';

    private const CATEGORIES = [
        BlogCategoryFixtures::DEVELOPMENT,
        BlogCategoryFixtures::DESIGN,
        BlogCategoryFixtures::PERFORMANCE,
        BlogCategoryFixtures::BUSINESS,
        BlogCategoryFixtures::SECURITY,
    ];

    private const TITLES = [
        'Construire une application Symfony durable',
        'Concevoir une interface vraiment accessible',
        'Accélérer son site sans tout reconstruire',
        'Préparer efficacement son projet web',
        'Sécuriser les formulaires de son application',
        'Structurer proprement son code métier',
        'Créer un design system cohérent',
        'Réduire le poids des images sur le web',
        'Définir le périmètre de son produit',
        'Protéger les données de ses utilisateurs',
        'Choisir les bons tests pour son application',
        'Améliorer la navigation sur mobile',
        'Comprendre les Core Web Vitals',
        'Faire évoluer un produit existant',
        'Éviter les failles les plus courantes',
        'Organiser une API facile à maintenir',
        'Rendre un parcours utilisateur plus fluide',
        'Optimiser le chargement des polices',
        'Prioriser les fonctionnalités utiles',
        'Mettre en place une authentification robuste',
        'Découpler les services de son application',
        'Choisir les bonnes couleurs pour une interface',
        'Mesurer les performances côté navigateur',
        'Réussir la refonte progressive d’un site',
        'Gérer correctement les droits d’accès',
        'Automatiser les tâches récurrentes avec Symfony',
        'Créer des composants réutilisables',
        'Mettre en cache sans servir de données obsolètes',
        'Transformer une idée en plan d’action',
        'Auditer la sécurité avant une mise en ligne',
    ];

    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();
        $repository = $manager->getRepository(Article::class);

        foreach (self::TITLES as $index => $title) {
            $status = $this->status($index);
            $slug = sprintf('article-blog-%02d', $index + 1);
            $article = $repository->findOneBy(['slug' => $slug]) ?? new Article();
            $article
                ->setCategory($this->getReference(self::CATEGORIES[$index % count(self::CATEGORIES)], Category::class))
                ->setTitle($title)
                ->setSlug($slug)
                ->setSummary('Des conseils concrets et directement applicables pour améliorer durablement la qualité de votre projet web.')
                ->setContent($this->content($title))
                ->setCoverImage(self::COVER_IMAGE)
                ->setStatus($status)
                ->setPublishedAt($this->publicationDate($status, $index, $now));

            if (29 === $index) {
                $article->archive($now->modify('-3 days'));
            } else {
                $article->restore();
            }

            $manager->persist($article);
            $this->addReference(sprintf('blog.article.%d', $index), $article);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [BlogCategoryFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['blog'];
    }

    private function content(string $title): string
    {
        $escapedTitle = htmlspecialchars($title, ENT_QUOTES);
        $sections = [
            'Définir le besoin avant de choisir une solution',
            'Observer le fonctionnement actuel',
            'Découper le travail en étapes maîtrisables',
            'Construire une première version utile',
            'Vérifier la qualité avec des critères mesurables',
            'Documenter les décisions importantes',
            'Préparer les évolutions futures',
            'Mesurer les résultats dans la durée',
        ];

        $content = sprintf(
            '<p>Un projet web durable ne dépend pas uniquement de la technologie choisie. Il repose surtout sur une compréhension précise du besoin, des décisions explicites et une méthode permettant de vérifier régulièrement que le produit reste utile. Dans cet article consacré à «&nbsp;%s&nbsp;», nous allons détailler une démarche concrète, applicable aussi bien à une création qu’à l’amélioration progressive d’une application existante.</p>',
            $escapedTitle,
        );

        foreach ($sections as $position => $section) {
            $content .= sprintf(
                '<h2>%s</h2><p>La première étape consiste à replacer chaque décision dans son contexte. Il faut identifier les personnes concernées, le problème réellement rencontré et le résultat attendu. Une solution apparemment élégante peut devenir coûteuse si elle répond à une hypothèse qui n’a jamais été vérifiée. Prendre le temps de formuler des objectifs observables permet de comparer les options avec davantage de recul et d’éviter que les préférences techniques prennent le dessus sur la valeur produite.</p><p>Cette analyse doit ensuite être traduite en actions suffisamment petites pour être réalisées, testées et corrigées rapidement. Chaque étape apporte une information nouvelle : un retour utilisateur, une mesure de performance, une contrainte métier oubliée ou une difficulté de maintenance. Ces informations ne sont pas des obstacles au projet ; elles permettent au contraire d’ajuster la trajectoire avant que les changements deviennent trop coûteux. Une progression régulière offre généralement plus de sécurité qu’une transformation massive menée sans points de contrôle.</p><h3>Point de contrôle %d</h3><ul><li>Décrire le résultat recherché avec des termes compréhensibles par toute l’équipe.</li><li>Choisir un indicateur simple permettant de constater une amélioration réelle.</li><li>Limiter le périmètre de la prochaine étape et préciser ce qui n’en fait pas partie.</li><li>Vérifier le comportement obtenu avant de commencer une nouvelle évolution.</li></ul>',
                htmlspecialchars($section, ENT_QUOTES),
                $position + 1,
            );
        }

        $content .= '<h2>Pour conclure</h2><p>La qualité d’un produit web vient rarement d’une décision spectaculaire. Elle résulte plutôt d’une succession de choix cohérents, compris par l’équipe et confrontés à des résultats concrets. En conservant un périmètre clair, des validations fréquentes et une documentation proportionnée aux enjeux, il devient possible de faire évoluer le projet sans perdre ce qui fonctionne déjà. Cette discipline améliore à la fois l’expérience des utilisateurs, la fiabilité du service et la capacité de l’équipe à intervenir sereinement dans la durée.</p>';

        return $content;
    }

    private function status(int $index): ArticleStatus
    {
        return match (true) {
            $index < 18, 29 === $index => ArticleStatus::PUBLISHED,
            $index < 22 => ArticleStatus::DRAFT,
            $index < 26 => ArticleStatus::REVIEW,
            default => ArticleStatus::SCHEDULED,
        };
    }

    private function publicationDate(ArticleStatus $status, int $index, \DateTimeImmutable $now): ?\DateTimeImmutable
    {
        return match ($status) {
            ArticleStatus::PUBLISHED => $now->modify(sprintf('-%d days', $index + 2)),
            ArticleStatus::SCHEDULED => $now->modify(sprintf('+%d days', $index - 24)),
            default => null,
        };
    }
}
