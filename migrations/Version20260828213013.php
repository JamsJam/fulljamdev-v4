<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260828213013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Importe les neuf expériences historiques du portfolio';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->experiences() as $experience) {
            $exists = $this->connection->fetchOne(
                'SELECT 1 FROM content_experience WHERE title = :title AND company = :company AND begin_at = :beginAt',
                ['title' => $experience['title'], 'company' => $experience['company'], 'beginAt' => $experience['beginAt']],
            );
            if (false !== $exists) {
                continue;
            }

            $about = $experience['about'];
            unset($experience['about']);
            $this->addSql(
                'INSERT INTO content_experience (type, title, company, contract_type, begin_at, end_at, about, is_visible) VALUES (:type, :title, :company, :contractType, :beginAt, :endAt, :about, 1)',
                [...$experience, 'about' => json_encode($about, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)],
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->experiences() as $experience) {
            $this->addSql(
                'DELETE FROM content_experience WHERE title = :title AND company = :company AND begin_at = :beginAt',
                ['title' => $experience['title'], 'company' => $experience['company'], 'beginAt' => $experience['beginAt']],
            );
        }
    }

    /** @return list<array{type: string, title: string, company: string, contractType: string, beginAt: string, endAt: ?string, about: list<string>}> */
    private function experiences(): array
    {
        return [
            ['type' => 'Freelance', 'title' => 'Développeur fullstack Symfony / Nuxt', 'company' => 'Bella GP', 'contractType' => 'Mission freelance en télétravail', 'beginAt' => '2025-05-01', 'endAt' => '2025-08-31', 'about' => ['🛠️ Api-platform, Symfony, Nuxt, Vue, GitHub Actions, CI/CD, Figma, SQL, SEO', '💻 Migration et refonte du back-end de bellagp.fr', '📝 Rédaction de cahiers des charges et documentation', '📊 Schématisation et mise en place de la base de données', '🔌 Désign d’une API Rest via Api-platform', '🚀 Déploiement et maintenance applicative', '💡 Propositions fonctionnelles pour le dashboard', '⚙️ Mise en place des environnements de développement/test/production']],
            ['type' => 'Freelance', 'title' => 'Développeur web Vue / Nuxt', 'company' => 'BellaGP', 'contractType' => 'Mission freelance en télétravail (binôme avec un designer)', 'beginAt' => '2024-12-01', 'endAt' => '2025-02-28', 'about' => ['💻 Vue.js, Nuxt.js, JavaScript, CSS, SASS', '🎨 Refonte graphique en collaboration avec un designer', '📝 Analyse de l’existant, propositions d’améliorations sur la maquette', '🖥️ Intégration front-end', '⚙️ Mise en place d’une pipeline de déploiement continu']],
            ['type' => 'Freelance', 'title' => 'Développeur web fullstack Symfony', 'company' => 'Association 100% famille', 'contractType' => 'Mission freelance en télétravail (en parallèle d’une mission de formation)', 'beginAt' => '2024-10-01', 'endAt' => '2025-01-31', 'about' => ['🎟️ Application e-billeterie pour la gestion des événements', '💻 Symfony 7, Twig, PHP, CSS, SASS, Stripe', '🗂️ Conception du schéma de la base de données', '⚙️ Choix de l’environnement (Symfony 7)', '🛠️ Mise en place de l’environnement de développement (Linux serveur, SQL, PHP)', '🗄️ Création de la base de données', '💻 Développement front-end et back-end', '🔄 Pipeline CI/CD (GitHub Actions)', '🌐 Mise en place de l’environnement de production', '⚡ Optimisation des performances', '🚀 Mise en production', '🔍 Suivis technique et référencement avec Ahref']],
            ['type' => 'Freelance', 'title' => 'Développeur web fullstack', 'company' => 'Guadeloupe Passion Caraïbes', 'contractType' => 'Mission freelance en télétravail (en parallèle d’une mission de formation)', 'beginAt' => '2024-09-01', 'endAt' => '2024-11-30', 'about' => ['🌴 Application web de vente d’activité et de voyage guadeloupepassioncaraïbes.fr', '💻 Symfony, Twig, PHP, CSS, SASS', '🔎 Analyse du besoin du client', '📝 Rédaction de cahiers des charges et documentation', '🎨 Maquettage d’application (Figma)', '🗂️ Conception du schéma de la base de données', '⚙️ Choix de l’environnement (Symfony 7)', '🛠️ Mise en place de l’environnement de développement (Linux serveur, SQL, PHP)', '🔄 Pipeline CI/CD (GitHub Actions)', '🗄️ Création de la base de données', '💻 Développement front-end et back-end', '⚡ Optimisation des performances', '🌐 Mise en place de l’environnement de production', '🚀 Mise en production']],
            ['type' => 'Freelence', 'title' => 'Formateur au titre professionnel Développeur web et mobile', 'company' => 'Colin’s Business', 'contractType' => 'Formation en présentiel au titre professionnel de développeur web et mobile', 'beginAt' => '2024-09-01', 'endAt' => '2025-01-31', 'about' => ['🏫 Formation en présentiel pour adultes', '📚 Modules couvrant : HTML, CSS, JavaScript, React, GitHub, Accessibilité, Mise en production, Conception de projet web']],
            ['type' => 'Freelance', 'title' => 'Développeur Front-end React / Next', 'company' => 'Titeca Beauport Finance', 'contractType' => 'Mission freelance obtenue via Malt, télétravail en autonomie', 'beginAt' => '2024-07-01', 'endAt' => '2024-08-31', 'about' => ['🛒 E-commerce Creolissime.fr, Design et création d’API via Strapi', '💻 Strapi, React, Next, JavaScript, TypeScript, Bitbucket, WordPress', '🔎 Analyse de l’API existante (WordPress, GraphQL), design de l’API REST (Strapi)', '🛠️ Réalisation : Création et configuration de l’API REST (Strapi)', '⚙️ Adaptation du code source à la nouvelle API (JavaScript, TypeScript, Next)', '🖥️ Création de composants React optimisés SEO']],
            ['type' => 'Freelance', 'title' => 'Développeur web WordPress', 'company' => 'Colin’s Business', 'contractType' => 'En freelance, travail en autonomie, télétravail partiel au travers de plusieurs projets', 'beginAt' => '2024-05-01', 'endAt' => '2024-06-30', 'about' => ['🎨 Refonte graphique du site vitrine alu-technologie.com et du site vitrine colis-avenue.com', '💻 HTML, CSS, WordPress, Elementor', '🔎 Analyse de l’existant, conception d’une nouvelle arborescence de pages', '🛠️ Création d’un thème personnalisé (Elementor Pro)', '📄 Mise en place de formulaire de saisie de produits et services', '📌 Projet : Site vitrine colis-avenue.com', '📚 Compétences acquises : HTML, CSS, JavaScript, PHP, WordPress, Elementor', '🔍 Analyse de l’existant', '🖌️ Création d’un thème personnalisé']],
            ['type' => 'Freelance', 'title' => 'Développeur Front-end', 'company' => 'Innovativ RH', 'contractType' => 'En freelance, travail en autonomie, télétravail partiel sur plusieurs projets', 'beginAt' => '2023-11-01', 'endAt' => '2024-03-31', 'about' => ['📄 Application de devis et de facture et application web de présentation des subventions régionales', '💻 HTML, CSS, JavaScript, SQL, PHP, Figma, React, React Native, Expo', '📝 Conception : Rédaction de cahiers des charges et documentation', '📥 Récupération des contenus spécifiques auprès du client', '🎨 Maquettage des pages', '⚙️ Choix de l’environnement technique', '🖥️ Développement Front-end de l’application', '🛠️ Mise en place d’un template de remplacement']],
            ['type' => 'CDI', 'title' => 'Développeur web PHP / Symfony', 'company' => 'Studio Okai', 'contractType' => 'CDI, travail en équipe avec un designer et un alternant développeur', 'beginAt' => '2023-02-01', 'endAt' => '2023-11-30', 'about' => ['🧑‍💻 Référent Développeur de l’entreprise, réalisation d’applications internes', '💻 SQL, HTML, CSS, PHP, Symfony, JavaScript, React Native, Electron.js, API Platform', '🗝️ Application web de gestion de mot de passe (Symfony + React Native)', '🌐 Sites web et applications promotionnels (Symfony + React Native)', '🎨 Analyse d’un besoin interne, conception de l’arborescence et design des pages (Figma)', '⚙️ Choix de l’environnement technique', '🗂️ Conception du schéma de base de données', '🛠️ Installation des environnements', '🖥️ Développement Front-end et Back-end', '🚀 Mise en production', '👨‍🏫 Formation d’un alternant développeur web']],
        ];
    }
}
