<?php

session_start();
require 'config.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: connexion.php");
    exit();
}

$client_id = $_SESSION['client_id'];
$stmt = $conn->prepare("SELECT * FROM comptebancaire WHERE clientId = ?");
$stmt->execute([$client_id]);
$comptes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 60vw;
        }
        h2, h3 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            color: #5cb85c;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .logout {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom']); ?>!</h2>
        <p class="logout"><a href="deconnexion.php">Se déconnecter</a></p>

        <h3>Vos Comptes</h3>
        <table>
            <tr>
                <th>Numéro de Compte</th>
                <th>Type</th>
                <th>Solde</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($comptes as $compte): ?>
            <tr>
                <td><?php echo htmlspecialchars($compte['numeroCompte']); ?></td>
                <td><?php echo htmlspecialchars($compte['typeDeCompte']); ?></td>
                <td><?php echo htmlspecialchars($compte['solde']); ?> €</td>
                <td>
                    <a href="depot_retrait.php?compte_id=<?php echo $compte['compteId']; ?>">Dépôt/Retrait</a> |
                    <a href="virement.php?compte_id=<?php echo $compte['compteId']; ?>">Virement</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <p><a href="creer_compte.php">Créer un nouveau compte</a></p>
    </div>
</body>
</html>
