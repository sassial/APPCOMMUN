-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 16, 2025 at 08:13 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appfinale`
--

-- --------------------------------------------------------

--
-- Table structure for table `dispositifs`
--

CREATE TABLE `dispositifs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('capteur','actionneur') NOT NULL,
  `nom_table_bdd` varchar(100) NOT NULL COMMENT 'Nom de la table dans la BDD distante (gusto_g5)',
  `seuil` float DEFAULT NULL,
  `unite` varchar(20) DEFAULT NULL,
  `type_alerte` varchar(20) NOT NULL DEFAULT 'superieur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dispositifs`
--

INSERT INTO `dispositifs` (`id`, `nom`, `type`, `nom_table_bdd`, `seuil`, `unite`, `type_alerte`) VALUES
(1, 'Son ambiant', 'capteur', 'Capteur_Son', NULL, NULL, 'superieur'),
(2, 'Lumière', 'capteur', 'CapteurLumiere', NULL, NULL, 'superieur'),
(3, 'Proximité', 'capteur', 'CapteurProximite', NULL, NULL, 'superieur'),
(4, 'Gaz', 'capteur', 'CapteurGaz', NULL, NULL, 'superieur'),
(5, 'Température & Humidité', 'capteur', 'capteur_temp_hum', NULL, NULL, 'superieur'),
(6, 'Lumière Principale', 'actionneur', 'Lampe1', NULL, NULL, 'superieur');

-- --------------------------------------------------------

--
-- Table structure for table `etats_actionneurs`
--

CREATE TABLE `etats_actionneurs` (
  `id_dispositif` int(11) NOT NULL,
  `etat` int(11) NOT NULL DEFAULT '0',
  `derniere_modif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `etats_actionneurs`
--

INSERT INTO `etats_actionneurs` (`id_dispositif`, `etat`, `derniere_modif`) VALUES
(1, 1, '2025-06-13 10:03:50'),
(2, 1, '2025-06-13 10:10:01'),
(3, 0, '2025-06-13 10:48:07'),
(4, 0, '2025-06-13 10:48:08'),
(5, 0, '2025-06-13 10:48:09');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'Gusto', 'admin@gusto.com', '$2y$10$pv1IYN9eyWINsn5t2UEY5OLEnBLIwcdElZR4RRL/fbrzzKcDWWu/C', 'admin'),
(2, 'User', 'Test', 'user@gusto.com', '$2y$10$7R7i9x4jL.Hk.5o8Q2.Fk.LwA7S/G2.tY/bI.c3t.iY.nO7B.c4.m', 'utilisateur'),
(3, 'Ruben', 'Legrand', 'legrandjacques.ruben@gmail.com', '$2y$10$QV5h1mksha7/5xkF50W5OuZxr4x5d4xmOBvkUbAtVUQEExFlK7SlK', 'utilisateur');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dispositifs`
--
ALTER TABLE `dispositifs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `etats_actionneurs`
--
ALTER TABLE `etats_actionneurs`
  ADD PRIMARY KEY (`id_dispositif`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dispositifs`
--
ALTER TABLE `dispositifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
