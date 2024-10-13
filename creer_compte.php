<?php

session_start();
require 'config.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: connexion.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numeroCompte = trim($_POST['numeroCompte']);
    $solde = floatval($_POST['solde']);
    $typeDeCompte = $_POST['typeDeCompte'];
    $clientId = $_SESSION['client_id'];

    if (strlen($numeroCompte) < 5 || strlen($numeroCompte) > 15) {
        $error = "Le numéro de compte doit contenir entre 5 et 15 caractères.";
    } elseif ($solde < 10 || $solde > 2000) {
        $error = "Le solde doit être compris entre 10 et 2000.";
    } elseif (!in_array($typeDeCompte, ['courant', 'epargne', 'entreprise'])) {
        $error = "Type de compte invalide.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM comptebancaire WHERE numeroCompte = ?");
        $stmt->execute([$numeroCompte]);
        if ($stmt->rowCount() > 0) {
            $error = "Ce numéro de compte existe déjà.";
        } else {
            $sql = "INSERT INTO comptebancaire (numeroCompte, solde, typeDeCompte, clientId) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            try {
                if ($stmt->execute([$numeroCompte, $solde, $typeDeCompte, $clientId])) {
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Une erreur est survenue lors de la création du compte.";
                }
            } catch (PDOException $e) {
                $error = "Erreur lors de l'insertion dans la base de données: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Compte Bancaire</title>
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
            width: 20vw;
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
        input[type="text"], input[type="number"], select {
            width: calc(100% - 20px);
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        <h2>Créer un Compte Bancaire</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="creer_compte.php" onsubmit="return validateForm()">
            <div>
                <label for="numeroCompte">Numéro de compte *</label>
                <input type="text" id="numeroCompte" name="numeroCompte" required>
            </div>
            <div>
                <label for="solde">Solde initial *</label>
                <input type="number" id="solde" name="solde" min="10" max="2000" required>
            </div>
            <div>
                <label for="typeDeCompte">Type de compte *</label>
                <select id="typeDeCompte" name="typeDeCompte" required>
                    <option value="courant">Courant</option>
                    <option value="epargne">Épargne</option>
                    <option value="entreprise">Entreprise</option>
                </select>
            </div>
            <button type="submit">Créer compte</button>
        </form>

        <script>
        function validateForm() {
            var numCompte = document.getElementById('numeroCompte').value;
            if (numCompte.length < 5 || numCompte.length > 15) {
                alert('Le numéro de compte doit contenir entre 5 et 15 caractères.');
                return false;
            }

            var solde = parseFloat(document.getElementById('solde').value);
            if (solde < 10 || solde > 2000) {
                alert('Le solde doit être compris entre 10 et 2000.');
                return false;
            }

            var typeCompte = document.getElementById('typeDeCompte').value;
            if (!['courant', 'epargne', 'entreprise'].includes(typeCompte)) {
                alert('Type de compte invalide.');
                return false;
            }

            return true;
        }
        </script>
    </div>
</body>
</html>
