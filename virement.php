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
$compte_emetteur = $stmt->fetch();

if (!$compte_emetteur) {
    die("Compte émetteur non trouvé.");
}

$stmt = $conn->prepare("SELECT * FROM comptebancaire WHERE compteId != ?");
$stmt->execute([$compte_id]);
$comptes_recepteurs = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $montant = floatval($_POST['montant']);
    $compte_recepteur_id = $_POST['compte_recepteur_id'];

    $stmt = $conn->prepare("SELECT * FROM comptebancaire WHERE compteId = ?");
    $stmt->execute([$compte_recepteur_id]);
    $compte_recepteur = $stmt->fetch();

    if (!$compte_recepteur) {
        $error = "Compte récepteur non trouvé.";
    } elseif ($montant <= 0) {
        $error = "Le montant doit être positif.";
    } elseif ($compte_emetteur['solde'] < $montant) {
        $error = "Solde insuffisant pour effectuer le virement.";
    } else {
        $conn->beginTransaction();

        try {
            $nouveau_solde_emetteur = $compte_emetteur['solde'] - $montant;
            $stmt = $conn->prepare("UPDATE comptebancaire SET solde = ? WHERE compteId = ?");
            $stmt->execute([$nouveau_solde_emetteur, $compte_id]);

            $nouveau_solde_recepteur = $compte_recepteur['solde'] + $montant;
            $stmt = $conn->prepare("UPDATE comptebancaire SET solde = ? WHERE compteId = ?");
            $stmt->execute([$nouveau_solde_recepteur, $compte_recepteur_id]);

            $conn->commit();
            header("Location: dashboard.php");
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Une erreur est survenue lors du virement.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Virement</title>
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
        input[type="number"], select {
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
        <h2>Virement depuis le compte <?php echo htmlspecialchars($compte_emetteur['numeroCompte']); ?></h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="virement.php?compte_id=<?php echo $compte_id; ?>">
            <div>
                <label for="montant">Montant *</label>
                <input type="number" id="montant" name="montant" min="0.01" step="0.01" required>
            </div>
            <div>
                <label for="compte_recepteur_id">Compte récepteur *</label>
                <select id="compte_recepteur_id" name="compte_recepteur_id" required>
                    <?php foreach ($comptes_recepteurs as $compte): ?>
                        <option value="<?php echo $compte['compteId']; ?>">
                            <?php echo htmlspecialchars($compte['numeroCompte']) . " - " . htmlspecialchars($compte['typeDeCompte']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Effectuer le virement</button>
        </form>
    </div>
</body>
</html>
