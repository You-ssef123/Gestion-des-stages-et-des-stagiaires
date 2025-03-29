<?php
session_start();

// Vérifiez si l'utilisateur est connecté et quel est son rôle
if (!isset($_SESSION['user_role'])) {
    header('Location: login.php');  // Redirige si l'utilisateur n'est pas connecté
    exit();
}

$user_role = $_SESSION['user_role'];  // Récupérez le rôle de l'utilisateur

// Connexion à la base de données
try {
    $connection = new PDO("mysql:host=localhost;dbname=stagiaire", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}

// Récupération des stages pour le select
try {
    $stagesQuery = $connection->query("SELECT id, title FROM stage");
    $stages = $stagesQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des stages : " . htmlspecialchars($e->getMessage()));
}

if (isset($_POST['submit'])) {
    $stage_id = htmlspecialchars($_POST['stage_id']);
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);

    try {
        $query = $connection->prepare("INSERT INTO stagiaires_valides (nom, prenom, email, stage_id, date_validation) VALUES (?, ?, ?, ?, ?)");
        $query->execute([$nom, $prenom, $email, $stage_id, date('Y-m-d H:i:s')]);

        echo "<script>alert('Candidature bien effectuée !'); window.location.href='AffectationStage.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Erreur lors de la soumission : " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Postuler au stage</title>
    <style>
        .form-container {
            width: 60%;
            margin: 30px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px #2a6496;
        }

        label {
            font-size: 16px;
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"],
        input[type="email"],
        select,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"] {
            background-color: #2a6496;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: rgb(36, 85, 128);
        }

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

        h1 {
            color: #444;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Menu de navigation */

        nav {
            background-color: #1d9bb2;
            padding: 15px 25px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            width: 100%;
            box-shadow: 0 2px 5px #2a6496;
        }

        nav ul {
            list-style-type: none;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        nav li {
            margin: 0 20px;
        }

        nav a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav a:hover {
            background-color: #2a6496;
        }

        /* Styles pour les cartes statistiques */
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }

        .card {
            margin-top: 40px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 200px;
            box-shadow: 0 2px 8px #2a6496;
        }

        .card h2 {
            font-size: 28px;
            color: #2a6496;
        }

        .card p {
            font-size: 18px;
            color: #555;
        }

        /* Styles du tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px #2a6496;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #2a6496;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        td a {
            color: #1E90FF;
            text-decoration: none;
        }

        td a:hover {
            text-decoration: underline;
        }

        /* Styles supplémentaires pour les boutons d'action */
        .action-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .action-links a {
            text-decoration: none;
            color: #fff;
            background-color: #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .action-links a:hover {
            background-color: #45a049;
        }

        .action-links a.refuser {
            background-color: #f44336;
        }

        .action-links a.refuser:hover {
            background-color: #e53935;
        }

        .logo {
            margin-right: 10px;
            transition: filter 0.3s;
            height: 70px;
            border: 2px solid #2a6496;
            box-shadow: 0 2px 5px #2a6496;
        }

        .barre {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media screen and (max-width: 1500px) {
            nav ul {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }

            nav li {
                margin: 5px;
                padding: 5px 10px;
            }

            nav a {
                font-size: 14px;
                padding: 8px 12px;
            }

            .logout {
                font-size: 14px;
                padding: 8px;
            }
        }

        @media screen and (max-width: 1200px) {
            nav {
                padding: 10px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: space-around;
            }

            nav li {
                margin: 3px;
                padding: 3px 8px;
            }

            nav a {
                font-size: 12px;
                padding: 6px 10px;
            }

            .logout {
                font-size: 12px;
                padding: 6px 8px;
            }
        }
        .llgo{
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .logout {
            padding: 10px;
            color: #2a6496;
            text-decoration: none;
            border-radius: 5px;
        }
        
        .logout:hover {
            color: red;
        }
    </style>
</head>

<body>
    <!-- Barre de navigation -->
    <div class="barre">
        <div class="llgo">
            <img src="logo.jpg" class="logo" alt="Logo">
            <a href="logout.php" class="logout">Déconnexion</a>
        </div>
        <nav>
            <ul>
                <li><a href="ValidationStagiaire.php">Validation des stages</a></li>
                <li><a href="AffectationStage.php">Affectation des stages</a></li>

                <?php if ($user_role === 'admin'): ?>
                    <li><a href="GestionDeComptes.php">Gestion des comptes</a></li>
                    <li><a href="AddStage.php">Gestion des stages</a></li>
                    <li><a href="dashboard.php">Tableau de bord</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <div class="form-container">
        <form method="post">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>

            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>

            <label for="stage_id">Sélectionner un stage :</label>
            <select id="stage_id" name="stage_id" required>
                <option value="">-- Choisissez un stage --</option>
                <?php foreach ($stages as $stage): ?>
                    <option value="<?= htmlspecialchars($stage['id']); ?>"><?= htmlspecialchars($stage['title']); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="submit" name="submit" value="Postuler">
        </form>
    </div>
</body>

</html>