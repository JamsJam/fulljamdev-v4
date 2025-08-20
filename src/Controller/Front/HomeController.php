<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Packages $packages, Request $request): Response
    {
        $baseUrl = $request->getSchemeAndHttpHost();
        $experiences = [
            [
                'type' => 'Freelance',
                'title' => 'Développeur fullstack Symfony / Nuxt',
                'entreprise' => 'Bella GP',
                'contraType' => 'Mission freelance en télétravail',
                'beginAt' => '2025-05-01',
                'endAt' => '2025-08-31',
                'about' => [
                    '🛠️ Api-platform, Symfony, Nuxt, Vue, GitHub Actions, CI/CD, Figma, SQL, SEO',
                    '💻 Migration et refonte du back-end de bellagp.fr',
                    '📝 Rédaction de cahiers des charges et documentation',
                    '📊 Schématisation et mise en place de la base de données',
                    "🔌 Désign d'une API Rest via Api-platform",
                    '🚀 Déploiement et maintenance applicative',
                    '💡 Propositions fonctionnelles pour le dashboard',
                    '⚙️ Mise en place des environnements de développement/test/production',
                ],
            ],
            [
                'type' => 'Freelance',
                'title' => 'Développeur web Vue / Nuxt',
                'entreprise' => 'BellaGP',
                'contraType' => 'Mission freelance en télétravail (binôme avec un designer)',
                'beginAt' => '2024-12-01',
                'endAt' => '2025-02-28',
                'about' => [
                    '💻 Vue.js, Nuxt.js, JavaScript, CSS, SASS',
                    '🎨 Refonte graphique en collaboration avec un designer',
                    '📝 Analyse de l’existant, propositions d’améliorations sur la maquette',
                    '🖥️ Intégration front-end',
                    '⚙️ Mise en place d’une pipeline de déploiement continu',
                ],
            ],
            [
                'type' => 'Freelance',
                'title' => 'Développeur web fullstack Symfony',
                'entreprise' => 'Association 100% famille',
                'contraType' => 'Mission freelance en télétravail (en parallèle d’une mission de formation)',
                'beginAt' => '2024-10-01',
                'endAt' => '2025-01-31',
                'about' => [
                    '🎟️ Application e-billeterie pour la gestion des événements',
                    '💻 Symfony 7, Twig, PHP, CSS, SASS, Stripe',
                    '🗂️ Conception du schéma de la base de données',
                    '⚙️ Choix de l’environnement (Symfony 7)',
                    '🛠️ Mise en place de l’environnement de développement (Linux serveur, SQL, PHP)',
                    '🗄️ Création de la base de données',
                    '💻 Développement front-end et back-end',
                    '🔄 Pipeline CI/CD (GitHub Actions)',
                    '🌐 Mise en place de l’environnement de production',
                    '⚡ Optimisation des performances',
                    '🚀 Mise en production',
                    '🔍 Suivis technique et référencement avec Ahref',
                ],
            ],
            [
                'type' => 'Freelance',
                'title' => 'Développeur web fullstack',
                'entreprise' => 'Guadeloupe Passion Caraïbes',
                'contraType' => 'Mission freelance en télétravail (en parallèle d’une mission de formation)',
                'beginAt' => '2024-09-01',
                'endAt' => '2024-11-30',
                'about' => [
                    "🌴 Application web de vente d'activité et de voyage guadeloupepassioncaraïbes.fr",
                    '💻 Symfony, Twig, PHP, CSS, SASS',
                    '🔎 Analyse du besoin du client',
                    '📝 Rédaction de cahiers des charges et documentation',
                    '🎨 Maquettage d’application (Figma)',
                    '🗂️ Conception du schéma de la base de données',
                    '⚙️ Choix de l’environnement (Symfony 7)',
                    '🛠️ Mise en place de l’environnement de développement (Linux serveur, SQL, PHP)',
                    '🔄 Pipeline CI/CD (GitHub Actions)',
                    '🗄️ Création de la base de données',
                    '💻 Développement front-end et back-end',
                    '⚡ Optimisation des performances',
                    '🌐 Mise en place de l’environnement de production',
                    '🚀 Mise en production',
                ],
            ],
            [
                'type' => 'Freelence',
                'title' => 'Formateur au titre professionnel Développeur web et mobile',
                'entreprise' => 'Colin’s Business',
                'contraType' => 'Formation en présentiel au titre professionnel de développeur web et mobile',
                'beginAt' => '2024-09-01',
                'endAt' => '2025-01-31',
                'about' => [
                    '🏫 Formation en présentiel pour adultes',
                    '📚 Modules couvrant : HTML, CSS, JavaScript, React, GitHub, Accessibilité, Mise en production, Conception de projet web',
                ],
            ],
            [
                'type' => 'Freelance',
                'title' => 'Développeur Front-end React / Next',
                'entreprise' => 'Titeca Beauport Finance',
                'contraType' => 'Mission freelance obtenue via Malt, télétravail en autonomie',
                'beginAt' => '2024-07-01',
                'endAt' => '2024-08-31',
                'about' => [
                    "🛒 E-commerce Creolissime.fr, Design et création d'API via Strapi",
                    '💻 Strapi, React, Next, JavaScript, TypeScript, Bitbucket, WordPress',
                    '🔎 Analyse de l’API existante (WordPress, GraphQL), design de l’API REST (Strapi)',
                    '🛠️ Réalisation : Création et configuration de l’API REST (Strapi)',
                    '⚙️ Adaptation du code source à la nouvelle API (JavaScript, TypeScript, Next)',
                    '🖥️ Création de composants React optimisés SEO',
                ],
            ],
            [
                'type' => 'Freelance',
                'title' => 'Développeur web WordPress',
                'entreprise' => 'Colin’s Business',
                'contraType' => 'En freelance, travail en autonomie, télétravail partiel au travers de plusieurs projets',
                'beginAt' => '2024-05-01',
                'endAt' => '2024-06-30',
                'about' => [
                    '🎨 Refonte graphique du site vitrine alu-technologie.com et du site vitrine colis-avenue.com',
                    '💻 HTML, CSS, WordPress, Elementor',
                    '🔎 Analyse de l’existant, conception d’une nouvelle arborescence de pages',
                    '🛠️ Création d’un thème personnalisé (Elementor Pro)',
                    '📄 Mise en place de formulaire de saisie de produits et services',
                    '📌 Projet : Site vitrine colis-avenue.com',
                    '📚 Compétences acquises : HTML, CSS, JavaScript, PHP, WordPress, Elementor',
                    '🔍 Analyse de l’existant',
                    '🖌️ Création d’un thème personnalisé',
                ],
            ],
            [
                'type' => 'Freelance',
                'title' => 'Développeur Front-end',
                'entreprise' => 'Innovativ RH',
                'contraType' => 'En freelance, travail en autonomie, télétravail partiel sur plusieurs projets',
                'beginAt' => '2023-11-01',
                'endAt' => '2024-03-31',
                'about' => [
                    '📄 Application de devis et de facture et application web de présentation des subventions régionales',
                    '💻 HTML, CSS, JavaScript, SQL, PHP, Figma, React, React Native, Expo',
                    '📝 Conception : Rédaction de cahiers des charges et documentation',
                    '📥 Récupération des contenus spécifiques auprès du client',
                    '🎨 Maquettage des pages',
                    '⚙️ Choix de l’environnement technique',
                    '🖥️ Développement Front-end de l’application',
                    '🛠️ Mise en place d’un template de remplacement',
                ],
            ],
            [
                'type' => 'CDI',
                'title' => 'Développeur web PHP / Symfony',
                'entreprise' => 'Studio Okai',
                'contraType' => 'CDI, travail en équipe avec un designer et un alternant développeur',
                'beginAt' => '2023-02-01',
                'endAt' => '2023-11-30',
                'about' => [
                    "🧑‍💻 Référent Développeur de l'entreprise, réalisation d'applications internes",
                    '💻 SQL, HTML, CSS, PHP, Symfony, JavaScript, React Native, Electron.js, API Platform',
                    '🗝️ Application web de gestion de mot de passe (Symfony + React Native)',
                    '🌐 Sites web et applications promotionnels (Symfony + React Native)',
                    '🎨 Analyse d’un besoin interne, conception de l’arborescence et design des pages (Figma)',
                    '⚙️ Choix de l’environnement technique',
                    '🗂️ Conception du schéma de base de données',
                    '🛠️ Installation des environnements',
                    '🖥️ Développement Front-end et Back-end',
                    '🚀 Mise en production',
                    "👨‍🏫 Formation d'un alternant développeur web",
                ],
            ],
        ];
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Jeremy Antoine',
            'description' => 'Developpeur web fullstack Freelance Symfony / React Vue, sur mesure et adapté. Service complet de la conception à la maintenance',
            'url' => $baseUrl,
            'image' => [
                '@type' => 'ImageObject',
                'url' => $baseUrl.$packages->getUrl('images/about-me2.png'),
                'width' => 400,
                'height' => 400,
            ],
            'jobTitle' => 'Développeur web fullstack Symfony',
            'sameAs' => [
                'https://github.com/JamsJam',
                'https://www.linkedin.com/in/jeremy-antoine-dwwm/',
                'https://www.malt.fr/profile/jeremyantoine',
            ],
            'worksFor' => [
                '@type' => 'Organization',
                'name' => 'Fulljam Dev',
                'description' => 'Entreprise de developpement web, maintenance et optimisation SEO',
                'url' => $baseUrl,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $baseUrl.$packages->getUrl('images/logos/logo-112.png'),
                    'width' => 112,
                    'height' => 112,
                    'caption' => 'Photo de Jeremy Antoine',
                ],
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'email' => 'contact@fulljamdev.fr',
                    'contactType' => 'Contact',
                ],
            ],
        ];

        $metaDescription = "J'assiste vos équipes, je développe et optimise votre projet de A a Z pour des performances et un référencement naturel optimal.";
        $metaTitle = 'Jeremy Antoine - Développeur Web Freelance & SEO Expert';

        return $this->render('front/home/index.html.twig', [
            'experiences' => $experiences,
            'jsonLD' => json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
            'metaDescription' => htmlspecialchars($metaDescription, ENT_NOQUOTES, 'UTF-8'),
            'metaTitle' => $metaTitle,
        ]);
    }
}
