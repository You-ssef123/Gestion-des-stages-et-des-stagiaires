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

// Récupération des statistiques
try {
    // Nombre total de stages
    $totalStages = $connection->query("SELECT COUNT(*) FROM stage")->fetchColumn();

    // Nombre total de stagiaires ayant postulé
    $totalStagiaires = $connection->query("SELECT COUNT(*) FROM stagiaire")->fetchColumn();

    // Nombre de stagiaires validés
    $totalStagiairesValides = $connection->query("SELECT COUNT(*) FROM stagiaires_valides")->fetchColumn();

    // Nombre de stagiaires refusés (basé sur ceux qui ne sont ni dans `stagiaire` ni dans `stagiaires_valides`)
    $totalStagiairesRefuses = $connection->query("SELECT COUNT(*) FROM stagiaire_deleted")->fetchColumn();

    // Nombre de gestionnaires
    $totalGestionnaires = $connection->query("SELECT COUNT(*) FROM gestionnaire")->fetchColumn();

    // Liste des stagiaires avec leur stage
    $query = $connection->query("
    SELECT s.id, s.nom, s.prenom, s.email, s.stage_id, st.title AS stage_name
    FROM stagiaire s
    LEFT JOIN stage st ON s.stage_id = st.id
");


    $stagiaires = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <style>
        /* Style de la barre de navigation */
        /* Reset des styles de base */
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

    <h1>📊 Tableau de Bord</h1>

    <!-- Statistiques -->
    <div class="stats">
        <div class="card">
            <h2><?= $totalStages; ?></h2>
            <p>Stages disponibles</p>
        </div>
        <div class="card">
            <h2><?= $totalStagiaires; ?></h2>
            <p>Stagiaires postulés</p>
        </div>
        <div class="card">
            <h2><?= $totalStagiairesValides; ?></h2>
            <p>Stagiaires validés</p>
        </div>
        <div class="card">
            <h2><?= $totalStagiairesRefuses; ?></h2>
            <p>Stagiaires refusés</p>
        </div>
        <div class="card">
            <h2><?= $totalGestionnaires; ?></h2>
            <p>Gestionnaires</p>
        </div>
    </div>

    <h2>📌 Liste des stagiaires</h2>
    <table>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Email</th>
            <th>Stage</th>
        </tr>
        <?php foreach ($stagiaires as $stagiaire): ?>
            <tr>
                <td><?= htmlspecialchars($stagiaire['nom']); ?></td>
                <td><?= htmlspecialchars($stagiaire['prenom']); ?></td>
                <td><?= htmlspecialchars($stagiaire['email']); ?></td>
                <td><?= htmlspecialchars($stagiaire['stage_name']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>


</body>

</html>