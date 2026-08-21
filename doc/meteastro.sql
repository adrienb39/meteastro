-- phpMyAdmin SQL Dump
-- version 5.2.3-2.fc44
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 21 août 2026 à 15:56
-- Version du serveur : 11.8.8-MariaDB
-- Version de PHP : 8.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `meteastro`
--

-- --------------------------------------------------------

--
-- Structure de la table `astronomie`
--

CREATE TABLE `astronomie` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `title_contenu` varchar(100) NOT NULL,
  `contenu` text NOT NULL,
  `filename` varchar(200) NOT NULL,
  `background_img` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `music_file` text DEFAULT NULL,
  `show_images` tinyint(1) NOT NULL DEFAULT 1,
  `enable_music` tinyint(1) NOT NULL DEFAULT 1,
  `background_mode` varchar(20) NOT NULL DEFAULT 'animated',
  `hud_feed_id` varchar(100) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL,
  `id_users` int(11) NOT NULL,
  `date_astronomie` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_connect`
--

CREATE TABLE `menu_connect` (
  `id` int(11) NOT NULL,
  `class` varchar(50) NOT NULL,
  `menu_name` text NOT NULL,
  `parent` int(11) NOT NULL,
  `url` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_principal`
--

CREATE TABLE `menu_principal` (
  `id` int(11) NOT NULL,
  `class` varchar(50) NOT NULL,
  `menu_name` text NOT NULL,
  `parent` int(11) NOT NULL,
  `url` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `meteorologie`
--

CREATE TABLE `meteorologie` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `title_contenu` varchar(100) NOT NULL,
  `contenu` text NOT NULL,
  `filename` varchar(200) NOT NULL,
  `background_img` varchar(255) DEFAULT NULL,
  `gallery_images` text DEFAULT NULL,
  `music_file` text DEFAULT NULL,
  `show_images` tinyint(1) NOT NULL DEFAULT 1,
  `enable_music` tinyint(1) NOT NULL DEFAULT 1,
  `background_mode` varchar(20) NOT NULL DEFAULT 'animated',
  `hud_feed_id` varchar(100) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL,
  `id_users` int(11) NOT NULL,
  `date_meteorologie` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `code` mediumint(50) NOT NULL,
  `status` text NOT NULL,
  `newsletter` tinyint(1) DEFAULT NULL,
  `last_version` varchar(10) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `astronomie`
--
ALTER TABLE `astronomie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ASTRONOMIE_USERTABLE` (`id_users`);

--
-- Index pour la table `menu_connect`
--
ALTER TABLE `menu_connect`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `menu_principal`
--
ALTER TABLE `menu_principal`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `meteorologie`
--
ALTER TABLE `meteorologie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_METEOROLOGIE_USERTABLE` (`id_users`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `astronomie`
--
ALTER TABLE `astronomie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menu_connect`
--
ALTER TABLE `menu_connect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menu_principal`
--
ALTER TABLE `menu_principal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `meteorologie`
--
ALTER TABLE `meteorologie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `astronomie`
--
ALTER TABLE `astronomie`
  ADD CONSTRAINT `FK_ASTRONOMIE_USERTABLE` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`);

--
-- Contraintes pour la table `meteorologie`
--
ALTER TABLE `meteorologie`
  ADD CONSTRAINT `FK_METEOROLOGIE_USERTABLE` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
