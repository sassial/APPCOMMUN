<?php

/**
 * Recherche un utilisateur par son email.
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @param string $email Email à rechercher.
 * @return array|null Données de l'utilisateur ou null si non trouvé.
 */
function rechercheParEmail(PDO $bdd, string $email): ?array {
    try {
        $statement = $bdd->prepare(
            'SELECT id, prenom, nom, email, password FROM utilisateurs WHERE email = :email'
        );
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (PDOException $e) {
        error_log("Erreur rechercheParEmail : " . $e->getMessage());
        return null;
    }
}

/**
 * Vérifie si un email existe déjà dans la base de données.
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @param string $email Email à vérifier.
 * @return bool True si l'email existe, sinon false.
 */
function emailExiste(PDO $bdd, string $email): bool {
    try {
        $statement = $bdd->prepare(
            'SELECT 1 FROM utilisateurs WHERE email = :email'
        );
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetchColumn() !== false;
    } catch (PDOException $e) {
        error_log("Erreur emailExiste : " . $e->getMessage());
        return false;
    }
}

/**
 * Ajoute un nouvel utilisateur dans la base de données.
 *
 * ATTENTION : le mot de passe doit être hashé avant appel.
 *
 * @param PDO $bdd Instance PDO de la base de données.
 * @param array $utilisateur Données de l'utilisateur (prenom, nom, email, password).
 * @return bool True en cas de succès, sinon false.
 */
function ajouteUtilisateur(PDO $bdd, array $utilisateur): bool {
    try {
        if (!isset($utilisateur['prenom'], $utilisateur['nom'], $utilisateur['email'], $utilisateur['password'])) {
            throw new InvalidArgumentException('Données utilisateur incomplètes.');
        }

        $statement = $bdd->prepare(
            'INSERT INTO utilisateurs (prenom, nom, email, password)
             VALUES (:prenom, :nom, :email, :password)'
        );

        $statement->bindValue(':prenom', $utilisateur['prenom'], PDO::PARAM_STR);
        $statement->bindValue(':nom', $utilisateur['nom'], PDO::PARAM_STR);
        $statement->bindValue(':email', $utilisateur['email'], PDO::PARAM_STR);
        $statement->bindValue(':password', $utilisateur['password'], PDO::PARAM_STR);

        return $statement->execute();
    } catch (Exception $e) {
        error_log("Erreur ajouteUtilisateur : " . $e->getMessage());
        return false;
    }
}
