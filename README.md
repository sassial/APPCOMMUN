

# 🚀 Projet Gusteau's : Surveillance Intelligente et Éco-Conçue

Bienvenue sur le projet de surveillance connectée du restaurant **Gusteau's**. Cette application web complète, développée en PHP, offre un suivi en temps réel de multiples capteurs (son, lumière, température, etc.) et permet de contrôler des actionneurs.

Le projet a été pensé avec une double exigence : offrir une **expérience utilisateur riche et réactive**, tout en intégrant des **principes d'éco-conception** pour minimiser son empreinte énergétique.

---

## 📑 Table des Matières

- [✨ Fonctionnalités Clés](#-fonctionnalités-clés)
- [🛠️ Technologies Utilisées](#-technologies-utilisées)
- [🌍 L'Éco-Conception au Cœur du Projet](#-léco-conception-au-cœur-du-projet)
- [⚙️ Installation (Guide Complet)](#-installation-guide-complet)
- [👤 Utilisation et Comptes](#-utilisation-et-comptes)
- [🔌 Architecture des APIs](#-architecture-des-apis)
- [💡 Prochaines Améliorations](#-prochaines-améliorations)

---

## ✨ Fonctionnalités Clés

### Pour tous les utilisateurs :
-   ✅ **Tableau de Bord Personnel** : Un accueil personnalisé avec un focus sur le capteur sonore, affichant des statistiques sur 24h et un historique visuel.
-   ✅ **Dashboard Multi-Capteurs** : Une vue centralisée et *live* de tous les capteurs actifs (son, lumière, proximité, gaz, température & humidité).
-   ✅ **Authentification Sécurisée** : Système d'inscription et de connexion complet, avec mots de passe hachés (BCrypt) et protection des sessions.
-   ✅ **Récupération de Mot de Passe** : Processus sécurisé par e-mail avec jetons (tokens) à durée de vie limitée.
-   ✅ **Service Externe Intégré** : Affiche une "citation du jour" pour enrichir l'interface.

### Pour les administrateurs :
-   ✅ **Gestion Centralisée des Dispositifs** : Une interface simple pour activer ou désactiver les capteurs qui apparaissent sur le tableau de bord.
-   ✅ **Configuration des Alertes par E-mail** : Possibilité de définir des seuils de déclenchement personnalisés pour chaque capteur directement depuis l'interface de gestion.
-   ✅ **Système d'Alertes Automatisé** : Envoi d'e-mails via SMTP lorsqu'un seuil est dépassé, permettant une réactivité immédiate.
-   ✅ **Contrôle des Actionneurs** : Possibilité d'allumer ou d'éteindre des dispositifs (ex: une lumière) à distance.

---

## 🛠️ Technologies Utilisées

-   **Backend** : PHP 8+
-   **Frontend** : HTML5, CSS3, JavaScript (vanilla)
-   **Base de Données** : MySQL / MariaDB (avec deux connexions : une locale pour la gestion, une distante pour les données des capteurs)
-   **Librairies** :
    -   `PHPMailer` : Pour l'envoi fiable d'e-mails via SMTP.
    -   `Chart.js` : Pour la visualisation dynamique et esthétique des données des capteurs.
    -   `Font Awesome` : Pour des icônes claires et intuitives.

---

## 🌍 L'Éco-Conception au Cœur du Projet

Un effort particulier a été porté sur la réduction de l'empreinte environnementale de l'application, en suivant les bonnes pratiques.

1.  **Optimisation Radicale des Données "Live"** :
    -   **Le problème :** Un système de mise à jour en temps réel peut être très gourmand en transférant de grandes quantités de données à chaque seconde.
    -   **Notre solution :** L'historique complet (50 points) n'est chargé **qu'une seule fois** au chargement de la page. Ensuite, l'API `api_get_latest.php` ne transfère que la **toute dernière mesure** (quelques centaines d'octets) toutes les 5 secondes.
    -   **Résultat :** Nous avons **réduit le poids des données de mise à jour de plus de 95%**, minimisant ainsi la consommation réseau et la charge serveur.

2.  **Arrêt des Requêtes Inutiles** :
    -   Grâce à l'API de visibilité de page JavaScript, les appels "live" à l'API sont **automatiquement suspendus** lorsque l'utilisateur n'est pas sur l'onglet de l'application. Les ressources ne sont consommées que lorsque c'est réellement utile.

3.  **Compression des Ressources** :
    -   Les images du site (`logo.jpg`, `photo.jpg`) ont été compressées pour réduire leur poids sans perte de qualité visible, accélérant le temps de chargement initial.

4.  **Optimisation des Requêtes SQL** :
    -   Toutes les requêtes de récupération de données historiques utilisent une clause `LIMIT` pour ne jamais charger plus de données que nécessaire, préservant ainsi les ressources de la base de données.

---

## ⚙️ Installation (Guide Complet)

### Prérequis
-   Un serveur local (MAMP, WAMP, XAMPP) avec PHP ≥ 7.4
-   Un serveur de base de données MySQL
-   [Composer](https://getcomposer.org/) pour la gestion des dépendances

### Étape 1 : Récupérer le code

Clonez ce dépôt dans le dossier `htdocs` (ou `www`) de votre serveur local.

git clone <URL_DU_DEPOT_GIT> APPFINALE
cd APPFINALE/APPCOMMUN

### Étape 2 : Installer les dépendances

Exécutez cette commande à la racine du dossier APPCOMMUN.

composer install
IGNORE_WHEN_COPYING_START


### Étape 3 : Configurer la base de données locale

Via phpMyAdmin, créez une base de données nommée APPFINALE.

Importez le fichier APPFINALE.sql fourni pour créer les tables utilisateurs, dispositifs, et etats_actionneurs.

Très important : Exécutez la requête suivante pour ajouter les colonnes nécessaires à la gestion des seuils et des unités :

ALTER TABLE `dispositifs`
  ADD `seuil` FLOAT NULL DEFAULT NULL AFTER `nom_table_bdd`,
  ADD `unite` VARCHAR(20) NULL DEFAULT NULL AFTER `seuil`,
  ADD `type_alerte` VARCHAR(20) NOT NULL DEFAULT 'superieur' AFTER `unite`;


### Étape 4 : Configurer le fichier config.php


Ouvrez le fichier config.php et remplissez les constantes avec vos propres informations :

Base de données locale (DB_..._LOCAL) : Les identifiants de votre serveur MAMP/WAMP.

Base de données commune (DB_..._COMMUN) : Les identifiants de la base de données distante qui reçoit les données des capteurs.

Paramètres SMTP (SMTP_...) : Crucial pour les e-mails. Utilisez une adresse Gmail avec un mot de passe d'application (et non votre mot de passe habituel).

Clé secrète (SECRET_KEY) : Changez cette chaîne pour une phrase longue et aléatoire pour sécuriser les jetons de réinitialisation.


### Étape 5 : Lancer l'application !

Démarrez votre serveur local et accédez à l'URL correspondante (ex: http://localhost/APPFINALE/APPCOMMUN/).


👤 Utilisation et Comptes


Compte Administrateur : Utilisez les identifiants présents dans APPFINALE.sql (par défaut admin@gusto.com / password) ou créez le vôtre et changez son rôle en 'admin' dans la base de données.

Compte Utilisateur : Créez un compte via la page d'inscription.


🔌 Architecture des APIs


Le projet expose plusieurs APIs pour interagir avec le système :

api_capteur.php (GET) : Endpoint pour que les capteurs physiques envoient leurs données. Il gère l'enregistrement et le déclenchement des alertes.

Exemple : .../api_capteur.php?type=son&valeur=85

api_get_latest.php (GET) : Utilisé par le frontend pour la mise à jour en direct. API éco-conçue qui ne renvoie que la dernière valeur des capteurs actifs.

api_actionneur.php (POST) : Endpoint sécurisé (session) pour changer l'état d'un actionneur.


💡 Prochaines Améliorations


Ce projet a des bases solides et peut encore être étendu :

Gestion des Utilisateurs : Une interface pour que l'admin puisse lister, modifier les rôles ou supprimer des utilisateurs.

Historique Détaillé : Une page dédiée pour chaque capteur, permettant de visualiser l'historique sur des périodes plus longues (semaine, mois) avec des options de filtrage.

Notifications Web Push : En plus des e-mails, envoyer des notifications directement dans le navigateur pour des alertes en temps réel.

