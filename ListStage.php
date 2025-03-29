<?php
try {
    $connection = new PDO("mysql:host=localhost;dbname=stagiaire", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $query = $connection->prepare("SELECT * FROM stage");
    $query->execute();
    $stages = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des stages</title>
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
            margin-bottom: 20px;
            color: #444;
        }

        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px #2a6496;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2a6496;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:nth-child(even) td {
            background-color: #f1f1f1;
        }

        tr:hover td {
            background-color: #e1e1e1;
        }



        /* Style du bouton de postuler */
        .postuler-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color:#2a6496;
            color: white;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .postuler-btn:hover {
            background-color:rgb(36, 85, 128);
        }

        /* Conteneur principal */
        
    </style>
</head>

<body>
    <h1>Liste des stages</h1>

        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Durée</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stages as $stage): ?>
                    <tr>
                        <td><?= htmlspecialchars($stage['title']); ?></td>
                        <td><?= nl2br(htmlspecialchars($stage['description'])); ?></td>
                        <td><?= htmlspecialchars($stage['durée']); ?></td>
                        <td><a class="postuler-btn" href="postuler.php?id=<?= htmlspecialchars($stage['id']); ?>">Postuler</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

</body>

</html>
