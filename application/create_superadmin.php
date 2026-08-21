<?php
// Inclure l'environnement CodeIgniter si nécessaire, ou faire simple :
$hash = password_hash('Admin123!', PASSWORD_DEFAULT);
echo "Hash généré : " . $hash . "<br>";

// Connexion à la base (à adapter avec vos identifiants)
$mysqli = new mysqli("localhost", "votre_utilisateur", "votre_mot_de_passe", "votre_base");
if ($mysqli->connect_error) die("Connexion DB échouée");

// Supprimer l'ancien super_admin
$mysqli->query("DELETE FROM users WHERE email = 'superadmin@diago.com'");

// Insérer avec le nouveau hash
$sql = "INSERT INTO users (username, email, password, role, entreprise_id, is_active, created_at) 
        VALUES ('superadmin', 'superadmin@diago.com', '$hash', 'super_admin', NULL, 'yes', NOW())";
if ($mysqli->query($sql)) {
    echo "Super Admin créé avec succès.<br>";
} else {
    echo "Erreur : " . $mysqli->error;
}
$mysqli->close();
?>