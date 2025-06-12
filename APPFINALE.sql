-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 12, 2025 at 09:43 AM
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
  `nom_table_bdd` varchar(100) NOT NULL COMMENT 'Nom table BDD distante',
  `unite` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dispositifs`
--

INSERT INTO `dispositifs` (`id`, `nom`, `type`, `nom_table_bdd`, `unite`) VALUES
(1, 'Température & Humidité', 'capteur', 'CapteurTempHum', '°C / %'),
(2, 'Lumière', 'capteur', 'CapteurLumiere', 'lux'),
(3, 'Proximité', 'capteur', 'CapteurProximite', 'cm'),
(4, 'Gaz', 'capteur', 'CapteurGaz', 'ppm'),
(5, 'Lumière Principale', 'actionneur', 'Lampe1', 'On/Off');

-- --------------------------------------------------------

--
-- Table structure for table `etats_actionneurs`
--

CREATE TABLE `etats_actionneurs` (
  `id_dispositif` int(11) NOT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=OFF, 1=ON',
  `derniere_modif` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `etats_actionneurs`
--

INSERT INTO `etats_actionneurs` (`id_dispositif`, `etat`, `derniere_modif`) VALUES
(5, 1, '2025-06-12 09:26:15');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'utilisateur'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `prenom`, `nom`, `email`, `password`, `role`) VALUES
(1, 'Alexandre', 'Sassi', 'alex29.sassi@gmail.com', '$2y$10$tFqey/Ie7IZ75HHxjQjuOO0xIbHXEIP1kn3hdZftziP.rz.jJNkg.', 'utilisateur'),
(3, 'S', 'O', 'os@gmail.com', '$2y$10$8nTxKdp/dsZcm2A6DbRFBeoJcox9Z0T025DTIgX6OaYmiFDMFJ7ki', 'utilisateur'),
(4, 'SA', 'OS', 'ossa@gmail.com', '$2y$10$3Srxf2wO/dYyydSChLdUPuRdYdh/Hr9oUv08btE04wwFMDyiVFW02', 'utilisateur'),
(5, 'Ruben', 'Legrandjacques', 'legrandjacques.ruben@gmail.com', '$2y$10$J4vLHX6QWPB8QFdCE0fCVOJRVykI3J1EkK.bk5aKq6jv08pSpOqVq', 'utilisateur'),
(6, 'Ruben', 'Legrandjacques', 'rlegrandjacques@juniorisep.com', '$2y$10$C/NAgRdIL7getjavTGzpvOxyJJozQYuh3O5AM.TbX4XS/EpzpdQL2', 'utilisateur');

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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dispositifs`
--
ALTER TABLE `dispositifs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
