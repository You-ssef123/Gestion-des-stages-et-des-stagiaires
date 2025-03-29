<?php
session_start();

// Vérifiez si l'utilisateur est connecté et quel est son rôle
if (!isset($_SESSION['user_role'])) {
    header('Location: login.php');  // Redirige si l'utilisateur n'est pas connecté
    exit();
}

$user_role = $_SESSION['user_role'];  // Récupérez le rôle de l'utilisateur
?>

<?php
try {
    // Connexion à la base de données
    $connection = new PDO("mysql:host=localhost;dbname=stagiaire", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Vérifier si un stage doit être supprimé
    if (isset($_GET['delete'])) {
        $stageId = $_GET['delete'];
        $query = $connection->prepare("DELETE FROM stage WHERE id = :id");
        $query->bindParam(':id', $stageId, PDO::PARAM_INT);
        $query->execute();

        // Alerte de succès après la suppression
        echo "<script>alert('Stage supprimé avec succès !');</script>";

        // Redirection vers la même page pour éviter la répétition de l'alerte
        echo "<script>window.location.href = 'AddStage.php';</script>";
        exit(); // Assure que le script s'arrête après la redirection
    }

    // Affichage des stages
    $query = $connection->prepare("SELECT * FROM stage");
    $query->execute();
    $stages = $query->fetchAll(PDO::FETCH_ASSOC);

    // Traitement du formulaire d'ajout de stage
    if (isset($_POST['save'])) {
        $query = $connection->prepare("INSERT INTO stage(title, description, durée) VALUES (:title, :description, :duree)");
        $query->bindParam(':title', $_POST['title']);
        $query->bindParam(':description', $_POST['description']);
        $query->bindParam(':duree', $_POST['durée']);
        $query->execute();

        // Alerte de succès après l'insertion
        echo "<script>alert('Stage ajouté avec succès !');</script>";
        // Rafraîchir la page pour afficher le nouveau stage
        echo "<script>window.location.href = 'AddStage.php';</script>";
        exit(); // Assure que le script s'arrête après la redirection
    }
} catch (PDOException $e) {
    echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des stages</title>
    <style>
        /* Réinitialisation des styles de base */
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
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

        form {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        input[type="text"],
        input[type="number"] {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #2a6496;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: rgb(33, 79, 119);
        }

        table tr td input {
            width: 100%;
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

        .supp {
            color: red;
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

        .llgo {
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
    <!-- Menu de navigation -->
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

    <h1>Liste des stages</h1>

    <form method="post">
        <table>
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Durée</th>
                <th>Action</th>
            </tr>

            <!-- Affichage des stages existants -->
            <?php foreach ($stages as $stage): ?>
                <tr>
                    <td><?= htmlspecialchars($stage['title']); ?></td>
                    <td><?= nl2br(htmlspecialchars($stage['description'])); ?></td>
                    <td><?= htmlspecialchars($stage['durée']); ?></td>
                    <td>
                        <!-- Lien pour supprimer le stage -->
                        <a class="supp" href="?delete=<?= htmlspecialchars($stage['id']); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce stage ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <!-- Formulaire d'ajout de stage -->
            <tr>
                <td><input type="text" id="title" name="title" required></td>
                <td><input type="text" id="description" name="description" required></td>
                <td><input type="text" id="durée" name="durée" required></td>
                <td><input type="submit" name="save" value="Ajouter"></td>
            </tr>
        </table>
    </form>

</body>

</html>