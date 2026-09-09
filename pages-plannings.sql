-- Donnees uniquement : pages, plannings, disponibilites et utilisateur.
-- Importer dans un schema migre avec tables cibles vides, sans --force.
-- Aucun rendez-vous, contact ou compte rendu.
SET @EXPORT_OLD_SQL_MODE = @@SESSION.sql_mode;
SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
SET @EXPORT_OLD_TIME_ZONE = @@SESSION.time_zone;
SET time_zone = '+00:00';
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
START TRANSACTION;

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `last_name`, `first_name`, `phone_number`, `company`, `job_title`) VALUES
(2, 'contact@fulljamdev.fr', '[\"ROLE_ADMIN\"]', '$2y$13$kaY/yq0XjbKTpRwkzlMAUOMDQD8TnUROufmSnSvM6sPaO.y.xfDAW', 'antoine', 'jeremy', '0786646817', 'Fulljam Dev', 'Developpeur fullstack');

INSERT INTO `planning` (`id`, `title`, `description`, `duration`, `gap`, `color`, `created_at`, `edited_at`, `is_active`, `slug`) VALUES
(23, 'Appels découverte', 'Premier échange pour découvrir le projet et définir les besoins.', 30, 10, '#6750A4', '2026-08-18 10:16:17', '2026-08-18 10:16:17', 1, '7095a964b0-appels-decouverte-abaf0e150b'),
(24, 'Suivi de projet', 'Points réguliers de suivi, validation et priorisation des prochaines étapes.', 45, 15, '#006C4C', '2026-08-18 10:16:17', '2026-08-18 10:16:17', 1, '37624a8a95-suivi-de-projet-76558ccb21'),
(25, 'Ateliers techniques', 'Sessions de travail consacrées à la conception et aux choix techniques.', 60, 20, '#B3261E', '2026-08-18 10:16:17', '2026-08-18 10:16:17', 1, '51abe2ba26-ateliers-techniques-d95472b3a7'),
(26, 'tguni', '<p>hnin,jo,o,ininouo,oio,io</p>', 25, 20, '#6439db', '2026-09-03 21:13:43', '2026-09-03 21:13:43', 0, '81ae9bde18-tguni-b1a18637a1');

INSERT INTO `availability` (`id`, `dow`, `start_hour`, `end_hour`, `planning_id`) VALUES
(82, 1, '09:00:00', '12:00:00', 23),
(83, 1, '14:00:00', '17:30:00', 23),
(84, 2, '09:00:00', '12:00:00', 23),
(85, 3, '14:00:00', '18:00:00', 23),
(86, 4, '09:00:00', '12:00:00', 23),
(87, 1, '10:00:00', '13:00:00', 24),
(88, 2, '14:00:00', '18:00:00', 24),
(89, 3, '10:00:00', '13:00:00', 24),
(90, 4, '14:00:00', '18:00:00', 24),
(91, 5, '09:00:00', '12:30:00', 24),
(92, 2, '09:00:00', '12:30:00', 25),
(93, 2, '14:00:00', '18:00:00', 25),
(94, 3, '09:00:00', '12:30:00', 25),
(95, 4, '09:00:00', '12:30:00', 25),
(96, 4, '14:00:00', '18:00:00', 25),
(97, 5, '09:00:00', '12:00:00', 25),
(98, 1, '00:13:00', '02:14:00', 26);

INSERT INTO `content_page` (`id`, `title`, `path`, `seo`) VALUES
(1, 'Accueil', 'accueil', '{\"title\": \"Développeur Fullstack Freelance JS & PHP | Jeremy Antoine\", \"noIndex\": false, \"description\": \"Jeremy Antoine, développeur fullstack freelance JavaScript & PHP. Applications web, sites internet, API, outils métier, automatisation et formation.\", \"canonicalUrl\": \"http://exemple.com\"}'),
(2, 'Contact', 'contact', '{\"title\": \"Contact\", \"noIndex\": false, \"description\": \"contact\", \"canonicalUrl\": \"http://exemple.com\"}'),
(3, 'About', 'about', '{\"title\": \"À propos\", \"noIndex\": false, \"description\": \"lorem ipsum\", \"canonicalUrl\": \"http://exemple.com\"}'),
(4, 'Blog', 'blog', '{\"title\": \"fdgbdfbdf\", \"noIndex\": false, \"description\": \"fdgbdfbdf\", \"canonicalUrl\": \"http://exemple.com\"}'),
(5, 'service - developpement', 'service/developpement', '{\"title\": \"Service - développement\", \"noIndex\": false, \"description\": \"sdvsdvsdv\", \"canonicalUrl\": \"http://exemple.com\"}'),
(6, 'service - seo', 'service/seo', '{\"title\": \"service - seo\", \"noIndex\": false, \"description\": \"dfsfzsdf\", \"canonicalUrl\": \"http://exemple.com\"}');

INSERT INTO `content_page_block` (`id`, `type`, `position`, `data`, `page_id`) VALUES
(1, 'hero.main', 0, '{\"cta1\": {\"href\": \"\", \"label\": \"Prendre rendez-vous\", \"target\": \"route\", \"routeName\": \"app_dashboard\", \"attributes\": [], \"routeParameters\": []}, \"cta2\": {\"href\": \"\", \"label\": \"Découvrir mes services\", \"target\": \"route\", \"routeName\": \"app_dashboard\", \"attributes\": [], \"routeParameters\": []}, \"text\": {\"content\": \"Applications web, sites internet, API, outils métier et automatisations sur mesure. J’accompagne entreprises et professionnels dans le développement de leurs projets web et propose également des formations adaptées à leurs besoins.\", \"attributes\": []}, \"image\": {\"alt\": \"sdgvsfbdf\", \"url\": null, \"title\": \"sfbsfbdfb\", \"source\": \"media\", \"mediaId\": \"ChatGPT-Image-27-aout-2026-16-34-36-6a904d5bac714.png\"}, \"title\": {\"level\": \"h1\", \"content\": \"Développeur Fullstack Freelance JS & PHP\", \"attributes\": []}, \"badges\": [{\"label\": \"sdvfbdfb\"}], \"reverse\": false}', 1),
(3, 'services.main', 1, '{\"cta\": {\"href\": \"\", \"label\": \"\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"text\": {\"content\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\", \"attributes\": []}, \"cards\": [{\"cta\": {\"href\": \"\", \"label\": \"sfvsfvdfsv\", \"target\": \"route\", \"routeName\": \"app_dashboard\", \"attributes\": [], \"routeParameters\": []}, \"logo\": {\"alt\": \"sdvsdvsdv\", \"url\": \"https://picsum.photos/50\", \"title\": \"sdvsdvdsv\", \"source\": \"url\", \"mediaId\": null}, \"text\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\", \"image\": null, \"title\": \"Développement web\"}, {\"cta\": {\"href\": \"\", \"label\": \"sfvsfvdfsv\", \"target\": \"route\", \"routeName\": \"app_home\", \"attributes\": [], \"routeParameters\": []}, \"logo\": {\"alt\": \"sdvsdvsdv\", \"url\": \"https://picsum.photos/50\", \"title\": \"sdvsdvsdv\", \"source\": \"url\", \"mediaId\": null}, \"text\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\", \"image\": null, \"title\": \"SEO et référencement\"}, {\"cta\": {\"href\": \"\", \"label\": \"qsdfds\", \"target\": \"route\", \"routeName\": \"app_home\", \"attributes\": [], \"routeParameters\": []}, \"logo\": {\"alt\": \"sdvsdvsdv\", \"url\": \"https://picsum.photos/50\", \"title\": \"sdvsdvsdv\", \"source\": \"url\", \"mediaId\": null}, \"text\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\", \"image\": null, \"title\": \"Automatisation et API\"}], \"title\": {\"level\": \"h2\", \"content\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit\", \"attributes\": []}, \"source\": \"static\", \"sourceKey\": \"featured_projects\"}', 1),
(4, 'cta.main', 2, '{\"cta\": {\"href\": \"https://fulljamdev.fr/book-meeting/7095a964b0-appels-decouverte-abaf0e150b\", \"label\": \"Je prends rendez-vous !\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"text\": {\"content\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliq\", \"attributes\": []}, \"title\": {\"level\": \"h2\", \"content\": \"Vous voulez en savoir plus ? Prenez rdv maintenant\", \"attributes\": []}}', 1),
(5, 'card_display.with_image', 4, '{\"cta\": {\"href\": \"https://fulljamdev.fr/book-meeting/7095a964b0-appels-decouverte-abaf0e150b\", \"label\": \"Voir tout mes projets\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"text\": {\"content\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\", \"attributes\": []}, \"cards\": [{\"cta\": {\"href\": \"https://fulljamdev.fr/book-meeting/7095a964b0-appels-decouverte-abaf0e150b\", \"label\": \"Voir le projet\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"logo\": null, \"text\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed\", \"image\": {\"alt\": \"sdfvsvsdv\", \"url\": \"https://picsum.photos/200\", \"title\": \"sdvsdvsdv\", \"source\": \"url\", \"mediaId\": null}, \"title\": \"Lorem ipsum\"}, {\"cta\": {\"href\": \"https://fulljamdev.fr/book-meeting/7095a964b0-appels-decouverte-abaf0e150b\", \"label\": \"Voir le projet\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"logo\": null, \"text\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed\", \"image\": {\"alt\": \"sdvsdvsdv\", \"url\": \"https://picsum.photos/200\", \"title\": \"sdvsdvsdv\", \"source\": \"url\", \"mediaId\": null}, \"title\": \"Lorem ipsum\"}, {\"cta\": {\"href\": \"https://fulljamdev.fr/book-meeting/7095a964b0-appels-decouverte-abaf0e150b\", \"label\": \"Voir le projet\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"logo\": null, \"text\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed\", \"image\": {\"alt\": \"sdvsdvsdvs\", \"url\": \"https://picsum.photos/200\", \"title\": \"sdvsdvsdvsdv\", \"source\": \"url\", \"mediaId\": null}, \"title\": \"Lorem ipsum\"}], \"title\": {\"level\": \"h2\", \"content\": \"Mes projets parles d\'eux meme !\", \"attributes\": []}, \"source\": \"static\", \"sourceKey\": \"featured_projects\"}', 1),
(6, 'faq.main', 6, '{\"text\": {\"content\": \"dsfkvmsf,vlldfjbod. sflvknsdflb sfvslnbsf sdlvnlsf b sdvslfknv\", \"attributes\": []}, \"items\": [{\"answer\": \"Je suis\", \"question\": \"Qui suis-je ?\"}, {\"answer\": \"Qui suis-je ?\", \"question\": \"Qui suis-je ?\"}, {\"answer\": \"Qui suis-je ?\", \"question\": \"Qui suis-je ?\"}, {\"answer\": \"Qui suis-je ?\", \"question\": \"Qui suis-je ?\"}, {\"answer\": \"Qui suis-je ?\", \"question\": \"Qui suis-je ?\"}], \"title\": {\"level\": \"h2\", \"content\": \"Foire au question\", \"attributes\": []}}', 1),
(7, 'planning.main', 0, '{\"text\": {\"content\": \"Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\", \"attributes\": []}, \"title\": {\"level\": \"h2\", \"content\": \"Yussuf\", \"attributes\": []}, \"planningId\": 23}', 2),
(8, 'faq.main', 1, '{\"text\": {\"content\": \"lorem ipsum\", \"attributes\": []}, \"items\": [{\"answer\": \"réponse\", \"question\": \"question\"}], \"title\": {\"level\": \"h2\", \"content\": \"<scswdvxcv\", \"attributes\": []}}', 2),
(9, 'hero.with_xl_image', 0, '{\"cta1\": {\"href\": \"http://exemple.com\", \"label\": \"sfvxfbxdfb\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"cta2\": {\"href\": \"\", \"label\": \"\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"text\": {\"content\": \"fbdfgnbdfgndsfbsv\", \"attributes\": []}, \"image\": {\"alt\": \"sdgvsfbdf\", \"url\": \"https://picsum.photos/300\", \"title\": \"sfbsfbdfb\", \"source\": \"url\", \"mediaId\": null}, \"title\": {\"level\": \"h1\", \"content\": \"Développeur Fullstack Freelance JS & PHP\", \"attributes\": []}, \"badges\": [], \"reverse\": false}', 3),
(10, 'resume.timeline', 1, '{\"title\": {\"level\": \"h1\", \"content\": \"Call to action\", \"attributes\": []}}', 3),
(11, 'cta.main', 2, '{\"cta\": {\"href\": \"https://fulljamdev.fr/book-meeting/7095a964b0-appels-decouverte-abaf0e150b\", \"label\": \"Je prends rendez-vous !\", \"target\": \"url\", \"routeName\": null, \"attributes\": [], \"routeParameters\": []}, \"text\": {\"content\": \"dvsdvsdvsdvsdv\", \"attributes\": []}, \"title\": {\"level\": \"h2\", \"content\": \"Vous voulez en savoir plus ? Prenez rdv maintenant\", \"attributes\": []}}', 3),
(12, 'faq.main', 3, '{\"text\": {\"content\": \"dfbdfbfgbdfvdfb\", \"attributes\": []}, \"items\": [{\"answer\": \"réponse\", \"question\": \"question\"}], \"title\": {\"level\": \"h2\", \"content\": \"Mes projets parles d\'eux meme !\", \"attributes\": []}}', 3),
(13, 'blog.latest', 5, '{\"text\": {\"content\": \"Conseils pratiques, retours d’expérience et réflexions autour de la création de produits web.\", \"attributes\": []}, \"title\": {\"level\": \"h2\", \"content\": \"Mon blog\", \"attributes\": []}}', 1),
(14, 'project.featured', 3, '{\"text\": {\"content\": \"Découvrez une sélection de projets conçus pour répondre à des besoins concrets.\", \"attributes\": []}, \"title\": {\"level\": \"h2\", \"content\": \"Mes projets\", \"attributes\": []}}', 1);

COMMIT;
SET SESSION sql_mode = @EXPORT_OLD_SQL_MODE;
SET time_zone = @EXPORT_OLD_TIME_ZONE;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
