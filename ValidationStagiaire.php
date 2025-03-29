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
// Connexion à la base de données
try {
    $connection = new PDO("mysql:host=localhost;dbname=stagiaire", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . htmlspecialchars($e->getMessage()));
}

// Supprimer un stagiaire (Refuser)
if (isset($_GET['refuser'])) {
    $stagiaire_id = intval($_GET['refuser']);
    try {
        // Récupérer les infos du stagiaire avant de le supprimer
        $query = $connection->prepare("SELECT * FROM stagiaire WHERE id = ?");
        $query->execute([$stagiaire_id]);
        $stagiaire = $query->fetch(PDO::FETCH_ASSOC);

        if ($stagiaire) {
            // Insérer dans la table stagiaire_deleted avant suppression
            $query = $connection->prepare("INSERT INTO stagiaire_deleted (nom, prenom, email, date_refus) VALUES (?, ?, ?, ?)");
            $query->execute([
                $stagiaire['nom'],
                $stagiaire['prenom'],
                $stagiaire['email'],
                date('Y-m-d H:i:s')  // La date et l'heure actuelle
            ]);

            // Supprimer le stagiaire de la table stagiaire
            $query = $connection->prepare("DELETE FROM stagiaire WHERE id = ?");
            $query->execute([$stagiaire_id]);

            echo "<script>alert('Stagiaire refusé et ajouté à la liste des supprimés.'); window.location.href='ValidationStagiaire.php';</script>";
        } else {
            echo "<script>alert('Erreur : Stagiaire introuvable.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erreur : " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// Valider un stagiaire et l'ajouter dans "stagiaires_valides"
if (isset($_GET['valider'])) {
    $stagiaire_id = intval($_GET['valider']);
    try {
        // Vérifier si la table "stagiaires_valides" existe
        $table_check = $connection->query("SHOW TABLES LIKE 'stagiaires_valides'");
        if ($table_check->rowCount() == 0) {
            die("<script>alert('La table stagiaires_valides n\'existe pas.'); window.location.href='ValidationStagiaire.php';</script>");
        }

        // Récupérer les infos du stagiaire
        $query = $connection->prepare("SELECT * FROM stagiaire WHERE id = ?");
        $query->execute([$stagiaire_id]);
        $stagiaire = $query->fetch(PDO::FETCH_ASSOC);

        if ($stagiaire) {
            // Insérer dans "stagiaires_valides" avec la date de validation actuelle
            $query = $connection->prepare("INSERT INTO stagiaires_valides (nom, prenom, email, stage_id, date_validation) VALUES (?, ?, ?, ?, ?)");
            $query->execute([
                $stagiaire['nom'],
                $stagiaire['prenom'],
                $stagiaire['email'],
                $stagiaire['stage_id'],
                date('Y-m-d H:i:s')  // Ajout de la date de validation actuelle
            ]);

            // Supprimer de la table "stagiaire" après validation
            $query = $connection->prepare("DELETE FROM stagiaire WHERE id = ?");
            $query->execute([$stagiaire_id]);

            echo "<script>alert('Stagiaire validé avec succès.'); window.location.href='ValidationStagiaire.php';</script>";
        } else {
            echo "<script>alert('Erreur : Stagiaire introuvable.');</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erreur : " . htmlspecialchars($e->getMessage()) . "');</script>";
    }
}

// Récupérer les stagiaires en attente de validation
try {
    $query = $connection->prepare("
    SELECT stagiaire.*, stage.title 
    FROM stagiaire 
    JOIN stage ON stagiaire.stage_id = stage.id
");

    $query->execute();
    $stagiaires = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Validation des stagiaires</title>
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

        @media screen and (max-width: 1600px) {
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

    <h1>Liste des stagiaires en attente</h1>

    <table>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>CV</th>
            <th>Lettre de motivation</th>
            <th>Stage ID</th>
            <th>Action</th>
        </tr>
        <?php foreach ($stagiaires as $stagiaire): ?>
            <tr>
                <td><?= htmlspecialchars($stagiaire['nom']); ?></td>
                <td><?= htmlspecialchars($stagiaire['prenom']); ?></td>
                <td><?= htmlspecialchars($stagiaire['email']); ?></td>
                <td><a href="<?= htmlspecialchars($stagiaire['cv']); ?>" target="_blank">Voir CV</a></td>
                <td><a href="<?= htmlspecialchars($stagiaire['lm']); ?>" target="_blank">Voir LM</a></td>
                <td><?= htmlspecialchars($stagiaire['title']); ?></td>
                <td>
                    <div class="action-links">
                        <a href="?valider=<?= htmlspecialchars($stagiaire['id']); ?>" onclick="return confirm('Valider ce stagiaire ?');">✅ Valider</a>
                        <a href="?refuser=<?= htmlspecialchars($stagiaire['id']); ?>" class="refuser" onclick="return confirm('Refuser ce stagiaire ?');">❌ Refuser</a>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>