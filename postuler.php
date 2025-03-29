<?php
// Connexion à la base de données
try {
    $connection = new PDO("mysql:host=localhost;dbname=stagiaire", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}

// Vérifier si l'ID du stage est présent
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du stage non spécifié !");
}

$stage_id = intval($_GET['id']);

if (isset($_POST['submit'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);

    // Gestion des fichiers CV et Lettre de Motivation
    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $cv_path = $upload_dir . time() . "_" . basename($_FILES['cv']['name']);
    $lm_path = $upload_dir . time() . "_" . basename($_FILES['lm']['name']);

    if (move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path) && move_uploaded_file($_FILES['lm']['tmp_name'], $lm_path)) {
        // Insertion des données en base avec stage_id
        try {
            $query = $connection->prepare("INSERT INTO stagiaire (stage_id, nom, prenom, email, cv, lm) VALUES (?, ?, ?, ?, ?, ?)");
            $query->execute([$stage_id, $nom, $prenom, $email, $cv_path, $lm_path]);

            echo "<script>alert('Votre candidature a été envoyée avec succès !'); window.location.href='ListStage.php';</script>";
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Erreur lors de la soumission : " . htmlspecialchars($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Erreur lors du téléchargement des fichiers.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Postuler au stage</title>
    <style>
        /* Reset de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #2a6496;
            margin-bottom: 20px;
        }

        .form-container {
            width: 60%;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"] {
            background-color: #f9f9f9;
        }

        input[type="submit"] {
            background-color: #2a6496;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color:rgb(36, 85, 128);
        }

        input[type="file"] {
            padding: 8px;
        }

        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Styles supplémentaires */
        .form-container input[type="file"] {
            border: none;
            background-color: transparent;
        }

        .form-container input[type="file"]:focus {
            border: 1px solid #4CAF50;
        }
    </style>
</head>

<body>
    <h1>Postuler pour ce stage</h1>

    <div class="form-container">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="stage_id" value="<?= htmlspecialchars($stage_id); ?>">

            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="cv">Télécharger votre CV (PDF uniquement) :</label>
            <input type="file" id="cv" name="cv" accept=".pdf" required>

            <label for="lm">Télécharger votre Lettre de motivation (PDF uniquement) :</label>
            <input type="file" id="lm" name="lm" accept=".pdf" required>

            <input type="submit" name="submit" value="Postuler">
        </form>
    </div>

</body>

</html>
