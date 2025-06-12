<?php
require_once(__DIR__ . '/connexion.php');

/**
 * Recherche un utilisateur par son email.
 * @param PDO $bdd L'objet de connexion à la BDD locale.
 * @param string $email L'email à rechercher.
 * @return array|null Les données de l'utilisateur ou null s'il n'est pas trouvé.
 */
function rechercheParEmail(PDO $bdd, string $email): ?array {
    $statement = $bdd->prepare('SELECT * FROM utilisateurs WHERE email = :email');
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/**
 * **NOUVELLE FONCTION ESSENTIELLE**
 * Vérifie si un email existe déjà dans la base de données.
 * @param PDO $bdd L'objet de connexion.
 * @param string $email L'email à vérifier.
 * @return bool True si l'email existe, false sinon.
 */
function emailExiste(PDO $bdd, string $email): bool {
    $statement = $bdd->prepare('SELECT 1 FROM utilisateurs WHERE email = :email');
    $statement->bindParam(':email', $email, PDO::PARAM_STR);
    $statement->execute();
    return $statement->fetchColumn() !== false;
}

/**
 * Ajoute un nouvel utilisateur dans la base de données.
 * @param PDO $bdd L'objet de connexion.
 * @param array $utilisateur Les données de l'utilisateur.
 * @return bool True en cas de succès, false en cas d'échec.
 */
function ajouteUtilisateur(PDO $bdd, array $utilisateur): bool {
    $query = 'INSERT INTO utilisateurs (prenom, nom, email, password) VALUES (:prenom, :nom, :email, :password)';
    $donnees = $bdd->prepare($query);
    $donnees->bindParam(":prenom", $utilisateur['prenom'], PDO::PARAM_STR);
    $donnees->bindParam(":nom", $utilisateur['nom'], PDO::PARAM_STR);
    $donnees->bindParam(":email", $utilisateur['email'], PDO::PARAM_STR);
    $donnees->bindParam(":password", $utilisateur['password'], PDO::PARAM_STR);
    return $donnees->execute();
}