-- phpMyAdmin SQL Dump
-- version 5.2.3-2.fc44
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : dim. 19 juil. 2026 à 10:34
-- Version du serveur : 11.8.8-MariaDB
-- Version de PHP : 8.5.8

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
  `verified` char(1) NOT NULL,
  `id_users` int(11) NOT NULL,
  `date_astronomie` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `astronomie`
--

INSERT INTO `astronomie` (`id`, `title`, `title_contenu`, `contenu`, `filename`, `background_img`, `gallery_images`, `music_file`, `show_images`, `enable_music`, `background_mode`, `hud_feed_id`, `verified`, `id_users`, `date_astronomie`) VALUES
(58, 'test', 'test', '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Incidunt suscipit soluta nisi quae architecto dolor quasi, nostrum rem placeat quaerat quam animi facilis laborum sequi necessitatibus asperiores, assumenda aperiam dignissimos.<br></p>', '', NULL, NULL, NULL, 1, 0, 'animated', NULL, 'y', 1, '0000-00-00 00:00:00'),
(59, 'test2', 'test2', '<p>test<br></p>', 'logo.png', NULL, NULL, NULL, 1, 0, 'animated', NULL, 'n', 1, '0000-00-00 00:00:00'),
(60, 'test', 'test3', '<p>test<br></p>', 'logo.png', NULL, NULL, NULL, 1, 0, 'animated', NULL, 'y', 1, '0000-00-00 00:00:00'),
(62, 'test', 'test4', '<p>test<br></p>', 'logo.png', NULL, NULL, 'Expedition-Long-Version-chosic.com_.mp3', 1, 1, 'animated', '', 'y', 1, '2024-04-07 11:11:28');

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(200) NOT NULL,
  `nom` varchar(200) NOT NULL,
  `prenom` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contact_gestimag`
--

CREATE TABLE `contact_gestimag` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `informations_entreprises_gestimag`
--

CREATE TABLE `informations_entreprises_gestimag` (
  `id` int(11) NOT NULL,
  `nom_entreprise` varchar(100) NOT NULL,
  `email_entreprise` varchar(100) NOT NULL,
  `numero_telephone_entreprise` varchar(10) NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `informations_entreprises_gestimag`
--

INSERT INTO `informations_entreprises_gestimag` (`id`, `nom_entreprise`, `email_entreprise`, `numero_telephone_entreprise`, `createdAt`) VALUES
(1, 'test', 'test@test.Com', '0602436562', '2024-11-06 20:48:42'),
(3, 'Meteastro', 'adrienb39@yahoo.com', '0602436562', '2024-11-06 21:01:35');

-- --------------------------------------------------------

--
-- Structure de la table `licenses`
--

CREATE TABLE `licenses` (
  `id_license` int(11) NOT NULL,
  `license_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `used_license` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `licenses`
--

INSERT INTO `licenses` (`id_license`, `license_key`, `created_at`, `used_license`) VALUES
(9, '13F87F6175105CAB7', '2024-11-14 05:47:44', 1),
(10, '121F610647AA13AC42F89BE9E0969B0D49CE3F1B242A88D576D3DFC36DA9A4A68', '2024-11-14 11:50:38', 1),
(11, '11781C9A0B3BFDE654EC1179BF641B5360AD4E74B34985B947CDAF8E6D4F868A6', '2024-11-14 20:27:12', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `logiciels`
--

CREATE TABLE `logiciels` (
  `id_logiciel` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `logiciels`
--

INSERT INTO `logiciels` (`id_logiciel`, `nom`, `description`, `prix`) VALUES
(1, 'Gestimag', 'Gestimag ERP & CRM est un logiciel moderne pour gérer votre activité', 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `membres`
--

CREATE TABLE `membres` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(200) NOT NULL,
  `mail` varchar(200) NOT NULL,
  `motdepasse` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membres`
--

INSERT INTO `membres` (`id`, `pseudo`, `mail`, `motdepasse`) VALUES
(6, 'adrien', 'adrienb39@yahoo.com', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'),
(7, 'test', 'test@yahoo.com', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3');

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

--
-- Déchargement des données de la table `menu_connect`
--

INSERT INTO `menu_connect` (`id`, `class`, `menu_name`, `parent`, `url`) VALUES
(1, 'nav-link', 'ACCUEIL', 0, '/#accueil'),
(2, 'nav-link', 'ASTRONOMIE', 0, '/divers/astronomie/astronomie.php'),
(3, 'nav-link', 'MÉTÉOROLOGIE', 0, '/divers/meteorologie/meteorologie.php'),
(4, 'nav-link', 'CONTACTS', 0, '/#contacts'),
(5, 'nav-link', 'CONTENUS', 0, '/redirect.php'),
(6, 'nav-link', 'COMPTE', 0, '#'),
(8, 'nav-link', 'PARAMÈTRES', 6, '#parametres/'),
(9, 'nav-link', 'DECONNEXION', 6, '/connexion/logout.php'),
(10, 'nav-link-gestimag', 'Gestereg', 0, 'https://gestereg.fr');

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

--
-- Déchargement des données de la table `menu_principal`
--

INSERT INTO `menu_principal` (`id`, `class`, `menu_name`, `parent`, `url`) VALUES
(1, 'nav-link', 'ACCUEIL', 0, '/#accueil'),
(2, 'nav-link', 'ASTRONOMIE', 0, '/divers/astronomie/astronomie.php'),
(3, 'nav-link', 'MÉTÉOROLOGIE', 0, '/divers/meteorologie/meteorologie.php'),
(4, 'nav-link-gestimag', 'Gestereg', 0, 'https://gestereg.fr'),
(5, 'nav-link', 'CONTACTS', 0, '/#contacts'),
(6, 'nav-link', 'CONNEXION', 0, '/connexion/login.php'),
(7, 'nav-link', 'INSCRIPTION', 0, '/connexion/signup.php');

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
  `verified` char(1) NOT NULL,
  `id_users` int(11) NOT NULL,
  `date_meteorologie` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `meteorologie`
--

INSERT INTO `meteorologie` (`id`, `title`, `title_contenu`, `contenu`, `filename`, `background_img`, `gallery_images`, `music_file`, `show_images`, `enable_music`, `background_mode`, `hud_feed_id`, `verified`, `id_users`, `date_meteorologie`) VALUES
(1, 'test 1', 'test 1', 'test 1', '', NULL, NULL, NULL, 1, 1, 'animated', NULL, 'y', 1, '0000-00-00 00:00:00'),
(2, 'test 2', 'test 2', 'test 2', '', NULL, NULL, NULL, 1, 1, 'animated', NULL, 'n', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `hash`, `active`) VALUES
(1, 'Adrien', 'Bruyere', 'adrienb39@yahoo.com', '$2y$10$GamgZUrxahZzafluy9bNrei6m5REASZL45jdEMfBfjxoVyjAa5SgG', 'dc6a70712a252123c40d2adba6a11d84', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users_gestimag`
--

CREATE TABLE `users_gestimag` (
  `id_users` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `status` varchar(10) DEFAULT NULL,
  `consent` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `admin` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users_gestimag`
--

INSERT INTO `users_gestimag` (`id_users`, `name`, `email`, `password`, `status`, `consent`, `created_at`, `admin`) VALUES
(8, 'Adrien Bruyere', 'adrienb39@yahoo.com', '$2y$10$lP4XTE5lpbOu3ksBlXzkduhmTKKFdu3lef5Ll6JtMXLHELc4NF6AG', NULL, NULL, '2024-11-13 20:13:42', 1),
(9, 'test', 'test@test.com', '$2y$10$XHFWZ3Qcs06RbL9qI8wzhOp6sUInuB1Ih.mRacEo30xNsr9d3tdFi', NULL, NULL, '2024-11-13 20:13:42', NULL),
(10, 'test', 'fzhi@frh.com', '$2y$10$pnTBFI15BdPs6qqH5vR0nOJuIW59vj8BjskfxAK3xS8ckWrVPFrc.', NULL, NULL, '2024-11-13 20:13:42', NULL),
(11, 'test', 'fho@zfh.com', '$2y$10$qg4rssWggwXNnbNA3Uc91uftMBSj1KqK4ZE/xQK5zhe33IFTcgqRe', NULL, NULL, '2024-11-13 20:13:42', NULL),
(12, 'test', 'hrzo@hvfr.com', '$2y$10$dG.Ag7xHnup2qzvUPXuBzuVMEe2IlFVfhkGeCkRpnqFl2.Evi6GwS', NULL, NULL, '2024-11-13 20:23:17', NULL),
(13, 'test', 'hefz@frh.com', '$2y$10$uQX8YQnXNJ2lyQgEsbdQyesHKLsxmrYwAcvCpZKnX/VKU97bxuqHu', NULL, NULL, '2024-11-13 20:28:56', NULL),
(14, 'test', 'ef@fhn.com', '$2y$10$90KjXx3UCxvh8vKVPIwk7uWhu5.Et/91hdOMFCw17v5Dw4fAJd4y2', NULL, NULL, '2024-11-13 20:33:08', NULL),
(15, 'test', 'fezhu@fj.com', '$2y$10$pLjIQ2N0MnCYOYbX0mhtculRwO3n9JQrA2ps8.O3NYPxEMDfl2XaO', 'verified', NULL, '2024-11-14 07:02:31', NULL),
(16, 'test', 'dj@djh.com', '$2y$10$gXSJPhngZAacCbMXfyfkj.hN8KQdXwkKegVZZcmeKih1fM7qpZrLW', 'verified', NULL, '2024-11-14 07:06:51', NULL),
(17, 'test', 'fj@fh.com', '$2y$10$lnMQ1QbIUd7LHmQwVspWo.a4ecnfoN4HrYBk77152LF2G421yGqXG', 'verified', NULL, '2024-11-14 12:10:15', NULL),
(18, 'Antoine Neret', 'antoine.neret@yahoo.Com', '$2y$10$CkHBmhxupLDOk7xHsjj38O0eZTM3K0rc0z58lnvJDH3kUanEOPLbG', 'verified', NULL, '2024-11-14 12:53:10', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users_software_license`
--

CREATE TABLE `users_software_license` (
  `id_users_software_license` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `software_id` int(11) NOT NULL,
  `license_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users_software_license`
--

INSERT INTO `users_software_license` (`id_users_software_license`, `user_id`, `software_id`, `license_id`) VALUES
(3, 18, 1, 10);

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
  `avertissement` text DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `datetime_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_users`, `name`, `email`, `password`, `code`, `status`, `avertissement`, `datetime`, `datetime_update`) VALUES
(1, 'Adrien Bruyere', 'adrienb39@yahoo.com', '$2y$10$8mJPrEtswhenphVdInjlxe6IwSCj0Ohg8z/iZ1d3VDy3f6/Y7rEz2', 0, 'verified', NULL, '0000-00-00 00:00:00', '2026-05-03 10:58:23'),
(5, 'gpezml', 'mfrz@dmz.com', '$2y$10$DbqncdXTOcxtdkMmKxSuC.NUUxUfy8kaql7nxVXVlUYDC/2FJtu.O', 0, 'verified', NULL, '0000-00-00 00:00:00', '2026-05-03 10:58:23');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `astronomie`
--
ALTER TABLE `astronomie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_ASTRONOMIE_users` (`id_users`);

--
-- Index pour la table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `contact_gestimag`
--
ALTER TABLE `contact_gestimag`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `informations_entreprises_gestimag`
--
ALTER TABLE `informations_entreprises_gestimag`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `licenses`
--
ALTER TABLE `licenses`
  ADD PRIMARY KEY (`id_license`);

--
-- Index pour la table `logiciels`
--
ALTER TABLE `logiciels`
  ADD PRIMARY KEY (`id_logiciel`);

--
-- Index pour la table `membres`
--
ALTER TABLE `membres`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `FK_METEOROLOGIE_users` (`id_users`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users_gestimag`
--
ALTER TABLE `users_gestimag`
  ADD PRIMARY KEY (`id_users`);

--
-- Index pour la table `users_software_license`
--
ALTER TABLE `users_software_license`
  ADD PRIMARY KEY (`id_users_software_license`),
  ADD KEY `FK_USERS_SOFTWARE_LICENSE_USERS_GESTIMAG` (`user_id`),
  ADD KEY `FK_USERS_SOFTWARE_LICENSE_SOFTWARE` (`software_id`),
  ADD KEY `FK_USERS_SOFTWARE_LICENSE_LICENSE` (`license_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT pour la table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `contact_gestimag`
--
ALTER TABLE `contact_gestimag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `informations_entreprises_gestimag`
--
ALTER TABLE `informations_entreprises_gestimag`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `licenses`
--
ALTER TABLE `licenses`
  MODIFY `id_license` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `logiciels`
--
ALTER TABLE `logiciels`
  MODIFY `id_logiciel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `membres`
--
ALTER TABLE `membres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `menu_connect`
--
ALTER TABLE `menu_connect`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `menu_principal`
--
ALTER TABLE `menu_principal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `meteorologie`
--
ALTER TABLE `meteorologie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users_gestimag`
--
ALTER TABLE `users_gestimag`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `users_software_license`
--
ALTER TABLE `users_software_license`
  MODIFY `id_users_software_license` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `astronomie`
--
ALTER TABLE `astronomie`
  ADD CONSTRAINT `FK_ASTRONOMIE_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`);

--
-- Contraintes pour la table `meteorologie`
--
ALTER TABLE `meteorologie`
  ADD CONSTRAINT `FK_METEOROLOGIE_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id_users`);

--
-- Contraintes pour la table `users_software_license`
--
ALTER TABLE `users_software_license`
  ADD CONSTRAINT `FK_USERS_SOFTWARE_LICENSE_LICENSE` FOREIGN KEY (`license_id`) REFERENCES `licenses` (`id_license`),
  ADD CONSTRAINT `FK_USERS_SOFTWARE_LICENSE_SOFTWARE` FOREIGN KEY (`software_id`) REFERENCES `logiciels` (`id_logiciel`),
  ADD CONSTRAINT `FK_USERS_SOFTWARE_LICENSE_USERS_GESTIMAG` FOREIGN KEY (`user_id`) REFERENCES `users_gestimag` (`id_users`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
