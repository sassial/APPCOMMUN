
# APPCOMMUN – Projet de Surveillance par Capteurs - Gusteau's

Ce projet est une application web complète développée en PHP, conçue pour surveiller et interagir avec divers capteurs et actionneurs en temps réel. Elle propose :
- Un tableau de bord dynamique
- Un système d'authentification robuste
- Une gestion centralisée des dispositifs
- Des alertes automatiques par email

---

## 📑 Table des Matières

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation (de Zéro au Site Fonctionnel)](#installation-de-zéro-au-site-fonctionnel)
  - Étape 1 : Récupérer le Code Source
  - Étape 2 : Installer les Dépendances
  - Étape 3 : Configurer la Base de Données Locale (APPFINALE)
  - Étape 4 : Configurer le Fichier `config.php`
- [Utilisation](#utilisation)
- [Comptes Utilisateurs](#comptes-utilisateurs)
- [Tester les Alertes par Email](#tester-les-alertes-par-email)
- [Structure du Projet](#structure-du-projet)
- [Détails des APIs](#détails-des-apis)
- [Dépannage](#dépannage)

---

## ✅ Fonctionnalités

### Fonctionnalités de base
- ✅ Page d'accueil avec tableau de bord
- ✅ Système d'authentification (admin/utilisateur)
- ✅ Gestion des capteurs et actionneurs
- ✅ Affichage des données en temps réel

### Fonctionnalités avancées
- ✅ Graphiques pour l’historique des capteurs
- ✅ Récupération météo via API externe
- 🟡 **Alertes par email** lorsque des seuils sont dépassés *(nécessite configuration SMTP)*

---

## 🛠️ Prérequis

Vous devez avoir les logiciels suivants installés :

- **PHP ≥ 7.4** (vérifiez avec `php -v`)
- **MySQL**
- **Un serveur local** :
  - Pour **Windows** : [WampServer](https://www.wampserver.com/)
  - Pour **macOS** : [MAMP](https://www.mamp.info/en/)
- **Composer** : [composer.org](https://getcomposer.org)
- **Git** : [git-scm.com](https://git-scm.com)

> 💡 **macOS :** Avec MAMP, placez le projet dans `/Applications/MAMP/htdocs` et accédez à l’URL via `http://localhost:8888/`.

---

## 🚀 Installation (de Zéro au Site Fonctionnel)

### Étape 1 : Récupérer le Code Source

```bash
# Sous Windows (ex : WAMP)
cd C:/wamp64/www

# Sous macOS (ex : MAMP)
cd /Applications/MAMP/htdocs

# Cloner le projet
git clone <URL_DU_DEPOT_GIT> APPCOMMUN
cd APPCOMMUN
````

### Étape 2 : Installer les Dépendances

```bash
composer install
```

Cela installera PHPMailer et les dépendances dans `vendor/`.

---

### Étape 3 : Configurer la Base de Données Locale (APPFINALE)

#### 1. Démarrer votre serveur local

* Lancer **MAMP** (macOS) ou **WAMP** (Windows)
* S’assurer qu’Apache et MySQL sont actifs

#### 2. Créer la base de données via phpMyAdmin

* URL : [http://localhost/phpmyadmin](http://localhost/phpmyadmin) (ou [http://localhost:8888/phpmyadmin](http://localhost:8888/phpmyadmin) sur mac)
* Créer une base nommée `APPFINALE` avec l’interclassement `utf8mb4_general_ci`

#### 3. Importer le fichier `APPFINALE.sql`

* Aller dans l’onglet **"SQL"**
* Copier tout le script fourni (ou le fichier `.sql`)
* **⚠️ Assurez-vous que la table `utilisateurs` est incluse**

#### 4. Ajouter les tables et données supplémentaires :

```sql
-- Table des dispositifs
CREATE TABLE `dispositifs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `type` enum('capteur','actionneur') NOT NULL,
  `nom_table_bdd` varchar(100) NOT NULL,
  `unite` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données par défaut
INSERT INTO `dispositifs` (`id`, `nom`, `type`, `nom_table_bdd`, `unite`) VALUES
(1, 'Son ambiant', 'capteur', 'CapteurSon', 'dB'),
(2, 'Lumière', 'capteur', 'CapteurLumiere', 'lux'),
(3, 'Proximité', 'capteur', 'CapteurProximite', 'cm'),
(4, 'Gaz', 'capteur', 'CapteurGaz', 'ppm'),
(5, 'Lumière Principale', 'actionneur', 'Lampe1', 'On/Off'),
(6, 'Température & Humidité', 'capteur', 'CapteurTempHum', '°C/%');

-- Table des états d'actionneurs
CREATE TABLE `etats_actionneurs` (
  `id_dispositif` int(11) NOT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT 0,
  `derniere_modif` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_dispositif`)
) ENGINE=InnoDB;

-- Ajout du rôle utilisateur
ALTER TABLE `utilisateurs` ADD `role` VARCHAR(50) NOT NULL DEFAULT 'utilisateur' AFTER `password`;

-- Donner un rôle admin à un utilisateur
UPDATE `utilisateurs` SET `role` = 'admin' WHERE `email` = 'alex29.sassi@gmail.com';
```

---

### Étape 4 : Configurer le Fichier `config.php`

Ouvrir le fichier à la racine du projet :

```php
<?php
define('DB_HOST_LOCAL', 'localhost');
define('DB_NAME_LOCAL', 'APPFINALE');
define('DB_USER_LOCAL', 'root');
define('DB_PASS_LOCAL', 'root'); // MAMP = 'root', WAMP = ''

define('DB_HOST_COMMUN', 'mysql-gusto.alwaysdata.net');
define('DB_NAME_COMMUN', 'gusto_g5');
define('DB_USER_COMMUN', 'gusto');
define('DB_PASS_COMMUN', 'RestoGustoG5');

// Configuration SMTP (email)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'votre.adresse.email@gmail.com');  // Remplacez ceci
define('SMTP_PASS', 'abcdefghijklmnop');               // Mot de passe d’application Gmail (16 caractères)
```

---

## 🌐 Utilisation

* Démarrer Apache/MySQL via MAMP ou WAMP
* Ouvrir votre navigateur :

  * Windows : [http://localhost/APPCOMMUN/](http://localhost/APPCOMMUN/)
  * macOS : [http://localhost:8888/APPCOMMUN/](http://localhost:8888/APPCOMMUN/)

---

## 👥 Comptes Utilisateurs

### Compte administrateur :

* Email : `alex29.sassi@gmail.com`
* Mot de passe : *(celui défini dans la base)*

### Compte utilisateur :

* À créer via la page d’inscription
* Rôle par défaut : `utilisateur`

---

## 🔔 Tester les Alertes par Email

Assurez-vous que `config.php` contient des identifiants SMTP valides. Ensuite, testez dans le navigateur :

```bash
# Alerte gaz
http://localhost/APPCOMMUN/api_capteur.php?type=gaz&valeur=1500

# Alerte température
http://localhost/APPCOMMUN/api_capteur.php?type=temperature&valeur=35&humidite=60
```

> Vérifiez votre boîte email pour voir si les alertes sont reçues.

---

## 📁 Structure du Projet

* `index.php` – Point d’entrée unique
* `controleurs/` – Logique applicative
* `modele/` – Accès base de données
* `vues/` – HTML / PHP pour affichage
* `api_*.php` – Endpoints pour capteurs/actionneurs
* `config.php` – Paramètres globaux
* `vendor/` – Librairies Composer (PHPMailer)

---

## 🔌 Détails des APIs

### `api_capteur.php`

* **Méthode :** `GET`
* **Paramètres :** `type`, `valeur`, `humidite` (optionnel)
* **Exemple :** `/api_capteur.php?type=son&valeur=75`

### `api_actionneur.php`

* **Méthode :** `POST`
* **Paramètres :** `id`, `etat` (1 = ON, 0 = OFF)

### `api_get_latest.php`

* **Méthode :** `GET`
* **Retour :** JSON contenant les dernières données

---

## 🛠️ Dépannage

### Erreur 500 ou page blanche

* Activer les erreurs en haut du fichier concerné :

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

* Consulter les logs de MAMP/WAMP (`php_error.log`)

### Emails non envoyés

* Vérifiez `SMTP_USER` et `SMTP_PASS` dans `config.php`
* Utilisez bien un **mot de passe d’application Gmail**
* Activez le mode debug de PHPMailer dans `controleurs/fonctions.php` :

```php
$mail->SMTPDebug = 2;
```

---

## 🧪 Testé sur

| OS      | Serveur local | Fonctionne |
| ------- | ------------- | ---------- |
| Windows | WAMP          | ✅          |
| macOS   | MAMP          | ✅          |

---

## 📬 Questions / Feedback

Pour toute remarque ou bug, contactez l’auteur du projet ou ouvrez une issue dans le dépôt Git.

---

```