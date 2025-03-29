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

    // Vérifier si un gestionnaire doit être supprimé
    if (isset($_GET['delete'])) {
        $gestionnaireId = filter_var($_GET['delete'], FILTER_VALIDATE_INT);
        if ($gestionnaireId) {
            $query = $connection->prepare("DELETE FROM gestionnaire WHERE id = :id");
            $query->bindParam(':id', $gestionnaireId, PDO::PARAM_INT);
            $query->execute();

            echo "<script>alert('Gestionnaire supprimé avec succès !');</script>";
            echo "<script>window.location.href = 'GestionDeComptes.php';</script>";
            exit();
        }
    }

    // Récupération des gestionnaires
    $query = $connection->prepare("SELECT * FROM gestionnaire");
    $query->execute();
    $gestionnaires = $query->fetchAll(PDO::FETCH_ASSOC);

    // Traitement du formulaire d'ajout de gestionnaire
    if (isset($_POST['save'])) {
        $nom = trim($_POST['nom']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if (!empty($nom) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password)) {
            $query = $connection->prepare("INSERT INTO gestionnaire(nom, email, password) VALUES (:nom, :email, :password)");
            $query->bindParam(':nom', $nom, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);
            $query->execute();

            echo "<script>alert('Gestionnaire ajouté avec succès !');</script>";
            echo "<script>window.location.href = 'GestionDeComptes.php';</script>";
            exit();
        } else {
            echo "<script>alert('Veuillez saisir des informations valides.');</script>";
        }
    }
} catch (PDOException $e) {
    echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "');</script>";
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des gestionnaires</title>
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
        input[type="email"],
        input[type="password"] {
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
            background-color: rgb(34, 80, 121);
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

    <h1>Liste des gestionnaires</h1>

    <form method="post">
        <table>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Mot de passe</th>
                <th>Action</th>
            </tr>

            <?php foreach ($gestionnaires as $gestionnaire): ?>
                <tr>
                    <td><?= htmlspecialchars($gestionnaire['nom']); ?></td>
                    <td><?= nl2br(htmlspecialchars($gestionnaire['email'])); ?></td>
                    <td><?= htmlspecialchars($gestionnaire['password']); ?></td>
                    <td>
                        <a class="supp" href="?delete=<?= htmlspecialchars($gestionnaire['id']); ?>"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce gestionnaire ?');">
                            Supprimer
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            <tr>
                <td><input type="text" id="nom" name="nom" required></td>
                <td><input type="email" id="email" name="email" required></td>
                <td><input type="password" id="password" name="password" required></td>
                <td><input type="submit" name="save" value="Ajouter"></td>
            </tr>
        </table>
    </form>

</body>

</html>