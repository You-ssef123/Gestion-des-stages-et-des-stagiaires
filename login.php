<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Connexion à la base de données
        $connection = new PDO('mysql:host=localhost;dbname=stagiaire;', "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Vérification si l'utilisateur est admin ou gestionnaire
        $query_admin = $connection->prepare("SELECT * FROM admin WHERE email = ? OR nom = ?");
        $query_admin->execute([$_POST['nom'], $_POST['nom']]); 
        $user_admin = $query_admin->fetch(PDO::FETCH_ASSOC);
        
        if ($user_admin && $_POST['password'] === $user_admin['password']) {
            // Si admin connecté
            $_SESSION['user_role'] = 'admin';
            $_SESSION['user_name'] = $user_admin['nom'];
            header('Location: acceuil.php');
            exit();
        }

        $query_gestionnaire = $connection->prepare("SELECT * FROM gestionnaire WHERE email = ? OR nom = ?");
        $query_gestionnaire->execute([$_POST['nom'], $_POST['nom']]);
        $user_gestionnaire = $query_gestionnaire->fetch(PDO::FETCH_ASSOC);
        
        if ($user_gestionnaire && $_POST['password'] === $user_gestionnaire['password']) {
            // Si gestionnaire connecté
            $_SESSION['user_role'] = 'gestionnaire';
            $_SESSION['user_name'] = $user_gestionnaire['nom'];
            header('Location: acceuil.php');
            exit();
        }

        // Si aucune correspondance
        echo "⚠️ Nom ou mot de passe incorrect.";

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }

        h2 {
            color: #444;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            width: 300px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #2a6496;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color:rgb(36, 82, 122);
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Se connecter</h2>
    <form action="Login.php" method="POST">
        <label for="nom">Nom ou Email :</label>
        <input type="text" name="nom" required><br><br>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required><br><br>
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
