<?php
session_start();
require 'config.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: connexion.php");
    exit();
}

$compte_id = $_GET['compte_id'];

$stmt = $conn->prepare("SELECT * FROM comptebancaire WHERE compteId = ? AND clientId = ?");
$stmt->execute([$compte_id, $_SESSION['client_id']]);
$compte = $stmt->fetch();

if (!$compte) {
    die("Compte non trouvé.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $montant = floatval($_POST['montant']);
    $type = $_POST['type'];

    if ($montant <= 0) {
        $error = "Le montant doit être positif.";
    } else {
        if ($type == 'retrait') {
            if ($compte['solde'] < $montant) {
                $error = "Solde insuffisant pour effectuer le retrait.";
            } else {
                $nouveau_solde = $compte['solde'] - $montant;
            }
        } elseif ($type == 'depot') {
            $nouveau_solde = $compte['solde'] + $montant;
        } else {
            $error = "Type de transaction invalide.";
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("UPDATE comptebancaire SET solde = ? WHERE compteId = ?");
            if ($stmt->execute([$nouveau_solde, $compte_id])) {
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Une erreur est survenue lors de la mise à jour du solde.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dépôt/Retrait</title>
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
            width: 30vw;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="number"] {
            width: calc(100% - 20px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="radio"] {
            margin-right: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dépôt/Retrait sur le compte <?php echo htmlspecialchars($compte['numeroCompte']); ?></h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="depot_retrait.php?compte_id=<?php echo $compte_id; ?>">
            <div>
                <label for="montant">Montant *</label>
                <input type="number" id="montant" name="montant" min="0.01" step="0.01" required>
            </div>
            <div>
                <label>Type de transaction *</label>
                <input type="radio" id="depot" name="type" value="depot" required>
                <label for="depot">Dépôt</label>
                <input type="radio" id="retrait" name="type" value="retrait" required>
                <label for="retrait">Retrait</label>
            </div>
            <button type="submit">Valider</button>
        </form>
    </div>
</body>
</html>
