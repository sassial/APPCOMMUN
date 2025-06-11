<?php

//on définit le nom de la table
$table = "utilisateurs";

/**
 * Recherche un utilisateur en fonction du nom passé en paramètre
 * @param PDO $bdd
 * @param string $nom
 * @return array
 */
function rechercheParNom(PDO $bdd, string $nom): array {
    
    $statement = $bdd->prepare('SELECT * FROM  utilisateurs WHERE nom = :nom');
    $statement->bindParam(":nom", $value);
    $statement->execute();
    
    return $statement->fetchAll();
    
}

/**
 * Récupère tous les enregistrements de la table utilisateurs
 * @param PDO $bdd
 * @return array
 */
function recupereTousUtilisateurs(PDO $bdd): array {
    $query = 'SELECT * FROM utilisateurs';
    return $bdd->query($query)->fetchAll();
}

/**
 * Ajoute un nouvel utilisateur dans la base de données
 * @param array $utilisateur
 */
function ajouteUtilisateur(PDO $bdd, array $utilisateur) {
    
    $query = ' INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)';
    $donnees = $bdd->prepare($query);
    $donnees->bindParam(":nom", $utilisateur['nom'], PDO::PARAM_STR);
    $donnees->bindParam(":email", $utilisateur['email'], PDO::PARAM_STR);
    $donnees->bindParam(":mot_de_passe", $utilisateur['mot_de_passe'], PDO::PARAM_STR);
    return $donnees->execute();
    
}

?>