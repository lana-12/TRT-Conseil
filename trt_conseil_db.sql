-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 22 mai 2024 à 10:32
-- Version du serveur : 10.4.25-MariaDB
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `trt_conseil_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `apply`
--

CREATE TABLE `apply` (
  `id` int(11) NOT NULL,
  `job_offer_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `apply`
--

INSERT INTO `apply` (`id`, `job_offer_id`, `candidate_id`, `active`, `created_at`) VALUES
(1, 1, 1, 1, '2024-05-22 09:06:25'),
(2, 2, 2, 1, '2024-05-22 09:25:57');

-- --------------------------------------------------------

--
-- Structure de la table `candidate`
--

CREATE TABLE `candidate` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `firstname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cv` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidate`
--

INSERT INTO `candidate` (`id`, `user_id`, `firstname`, `lastname`, `cv`, `active`, `city`) VALUES
(1, 5, 'Jolie', 'Angelina', 'CV-664d9969a00e2.pdf', 1, 'Montpellier'),
(2, 10, 'Giac', 'Ange', 'CV-664d9b41c9ea9.pdf', 1, 'CANDILLARGUES'),
(3, 9, 'Kamelot', 'Arthur', 'CV-664d9ba87ed01.pdf', 1, 'Mauguio');

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contact`
--

INSERT INTO `contact` (`id`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'martin-trtconseil@outlook.fr', 'test', 'Mon message', '2024-05-22 09:24:31');

-- --------------------------------------------------------

--
-- Structure de la table `job_offer`
--

CREATE TABLE `job_offer` (
  `id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `job_offer`
--

INSERT INTO `job_offer` (`id`, `recruiter_id`, `title`, `city`, `content`, `active`, `created_at`) VALUES
(1, 1, 'Cuisinier(ère)', 'Montpellier', 'Savoir faire de qualité,', 1, '2024-05-22 08:59:37'),
(2, 2, 'Serveur(se)', 'Mauguio', 'Dans un restaurant spécialisé Corse, nous recherchons ...', 1, '2024-05-22 09:19:15'),
(3, 2, 'Cuisinier(ère)', 'Mauguio', 'Savoir faire de qualité', 0, '2024-05-22 09:19:49'),
(4, 2, 'Employé Polyvalent', 'Mauguio', 'Aide en cuisine et en salle', 1, '2024-05-22 09:20:33'),
(5, 3, 'Employé Polyvalent', 'Lansargues', 'Aide en cuisine et en salle', 0, '2024-05-22 09:22:29'),
(6, 3, 'Cuisinier(ère)', 'Lansargues', 'Savoir faire de qualité', 1, '2024-05-22 09:22:50');

-- --------------------------------------------------------

--
-- Structure de la table `recruiter`
--

CREATE TABLE `recruiter` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name_company` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `recruiter`
--

INSERT INTO `recruiter` (`id`, `user_id`, `name_company`, `address`, `zip_code`, `city`, `active`) VALUES
(1, 4, 'Le Jardin des Frangins', '2 rue des Loriots', '34000', 'Montpellier', 1),
(2, 6, 'Maky', '26 boulevard du papa noel', '34130', 'Mauguio', 1),
(3, 7, 'Le Paradis bleu', '7 chemin des nuages', '34130', 'Lansargues', 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `type`, `token`, `active`) VALUES
(1, 'giac@giac.com', '[\"ROLE_RECRUITER\"]', '$2y$13$PG3hKnD96wv6WJYZiBOnku0T00/ljidCtGhStdPHI5X3TmGZ5nPjG', NULL, NULL, 0),
(2, 'martin-trtconseil@outlook.fr', '[\"ROLE_CONSULTANT\"]', '$2y$13$vhmJofGO5DKGyModMZ4Kvu95uLmLS2X1jEjGdjqfhwZk9CO9ejpv.', NULL, NULL, 1),
(4, 'recruteur1@exemple.fr', '[\"ROLE_RECRUITER\"]', '$2y$13$er/L4I2HBpxEgSKC7rY5Q.2vVVPxjT1CExsHVKVlJE62z3BzPXxMm', NULL, NULL, 1),
(5, 'candidat1@exemple.fr', '[\"ROLE_CANDIDATE\"]', '$2y$13$pAqkxSTxXuo0/0wsTYD/y.RJEo6G4XiKcQYEnRDhBobNJG.ekldEm', NULL, NULL, 1),
(6, 'recruteur2@exemple.fr', '[\"ROLE_RECRUITER\"]', '$2y$13$QnJNhwh9DsoJH3lrGfcKYeoIf0fhiB2gvWuKAQj6JzLCk.lz0MykG', NULL, NULL, 1),
(7, 'recruteur3@exemple.fr', '[\"ROLE_RECRUITER\"]', '$2y$13$4lvdEq8NiJ8mHedAIXRoe.8bwzdaY5u73NU497tjE85/ujuK5RNcK', NULL, NULL, 1),
(8, 'recruteur4@exemple.fr', '[\"ROLE_RECRUITER\"]', '$2y$13$xnvT8OFd7UA/lGYcMVHlce4ieTwr4HxyYt8CoIs6b61UaWsUuCUcS', NULL, NULL, 0),
(9, 'candidat2@exemple.fr', '[\"ROLE_CANDIDATE\"]', '$2y$13$klV8dOtNuT8rHIPtkSRYe.kHcjBB7Ff22CEWFwfMb/B1ecSNGJ4n6', NULL, NULL, 1),
(10, 'candidat3@exemple.fr', '[\"ROLE_CANDIDATE\"]', '$2y$13$pQJ94LJU5M668KveXiVgLOLYRY3mXdV.3U1shMvSv72KwHEgGdMhW', NULL, NULL, 1),
(11, 'candidat4@exemple.fr', '[\"ROLE_CANDIDATE\"]', '$2y$13$7RIg0/OfrtovLLeH7VsYOevZsTcrNF0N8nyHIfVmcI.RSCi.MJVz6', NULL, NULL, 0),
(12, 'candidat5@exemple.fr', '[\"ROLE_CANDIDATE\"]', '$2y$13$yMK8GN.npO5y5xjjl3I9H.R1XW7ZLIUY8Z53k/3R0Iubu1jM/M.0.', NULL, NULL, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `apply`
--
ALTER TABLE `apply`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_BD2F8C1F3481D195` (`job_offer_id`),
  ADD KEY `IDX_BD2F8C1F91BD8781` (`candidate_id`);

--
-- Index pour la table `candidate`
--
ALTER TABLE `candidate`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C8B28E443124B5B6` (`lastname`),
  ADD KEY `IDX_C8B28E44A76ED395` (`user_id`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `job_offer`
--
ALTER TABLE `job_offer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_288A3A4E156BE243` (`recruiter_id`);

--
-- Index pour la table `recruiter`
--
ALTER TABLE `recruiter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_DE8633D8A76ED395` (`user_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `apply`
--
ALTER TABLE `apply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `candidate`
--
ALTER TABLE `candidate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `job_offer`
--
ALTER TABLE `job_offer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `recruiter`
--
ALTER TABLE `recruiter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `apply`
--
ALTER TABLE `apply`
  ADD CONSTRAINT `FK_BD2F8C1F3481D195` FOREIGN KEY (`job_offer_id`) REFERENCES `job_offer` (`id`),
  ADD CONSTRAINT `FK_BD2F8C1F91BD8781` FOREIGN KEY (`candidate_id`) REFERENCES `candidate` (`id`);

--
-- Contraintes pour la table `candidate`
--
ALTER TABLE `candidate`
  ADD CONSTRAINT `FK_C8B28E44A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `job_offer`
--
ALTER TABLE `job_offer`
  ADD CONSTRAINT `FK_288A3A4E156BE243` FOREIGN KEY (`recruiter_id`) REFERENCES `recruiter` (`id`);

--
-- Contraintes pour la table `recruiter`
--
ALTER TABLE `recruiter`
  ADD CONSTRAINT `FK_DE8633D8A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
