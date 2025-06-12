<?php
// Fichier : generer_hash.php

// Mettez ici le mot de passe que vous voulez hacher
$mot_de_passe_en_clair = "GUSTO";

// Génération du hash avec l’algorithme par défaut (actuellement BCRYPT)
$hash = password_hash($mot_de_passe_en_clair, PASSWORD_DEFAULT);

// Affichage du hash
echo "Le hash du mot de passe est : " . $hash;
?>
