-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 12, 2025 at 03:00 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `APPFINALE`
--

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--
-- Cette table stocke les informations des utilisateurs, y compris leur rôle.
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--
-- Un utilisateur 'admin' et un utilisateur 'user' pour les tests.
-- Le mot de passe pour les deux est 'password'
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'Gusto', 'admin@gusto.com', '$2y$10$7R7i9x4jL.Hk.5o8Q2.Fk.LwA7S/G2.tY/bI.c3t.iY.nO7B.c4.m', 'admin'),
(2, 'User', 'Test', 'user@gusto.com', '$2y$10$7R7i9x4jL.Hk.5o8Q2.Fk.LwA7S/G2.tY/bI.c3t.iY.nO7B.c4.m', 'utilisateur');

-- --------------------------------------------------------

--
-- Table structure for table `dispositifs`
--
-- Cette table est un catalogue de tous les capteurs et actionneurs possibles.
--

CREATE TABLE `dispositifs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` enum('capteur','actionneur') NOT NULL,
  `nom_table_bdd` varchar(100) NOT NULL COMMENT 'Nom de la table dans la BDD distante (gusto_g5)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dispositifs`
--

INSERT INTO `dispositifs` (`id`, `nom`, `type`, `nom_table_bdd`) VALUES
(1, 'Son ambiant', 'capteur', 'CapteurSon'),
(2, 'Lumière', 'capteur', 'CapteurLumiere'),
(3, 'Proximité', 'capteur', 'CapteurProximite'),
(4, 'Gaz', 'capteur', 'CapteurGaz'),
(5, 'Température & Humidité', 'capteur', 'CapteurTempHum'),
(6, 'Lumière Principale', 'actionneur', 'Lampe1');

-- --------------------------------------------------------

--
-- Table structure for table `etats_actionneurs`
--
-- Cette table stocke l'état actuel de chaque dispositif (actif/inactif, seuil, ON/OFF).
-- 'etat' = 0 signifie inactif.
-- 'etat' = 1 signifie actif (pour un capteur) ou ON (pour un actionneur).
-- 'etat' > 1 pour un capteur représente sa valeur de seuil.
--

CREATE TABLE `etats_actionneurs` (
  `id_dispositif` int(11) NOT NULL,
  `etat` int(11) NOT NULL DEFAULT 0,
  `derniere_modif` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dispositifs`
--
ALTER TABLE `dispositifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;