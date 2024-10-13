<?php

session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mdp'];

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
        $error = "Tous les champs marqués d'un * sont obligatoires.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM client WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Un compte avec cet email existe déjà.";
        } else {
            $hashed_mdp = password_hash($mdp, PASSWORD_DEFAULT);

            $sql = "INSERT INTO client (nom, prenom, telephone, email, mdp) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt->execute([$nom, $prenom, $telephone, $email, $hashed_mdp])) {
                header("Location: connexion.php");
                exit();
            } else {
                $error = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
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
        input[type="text"], input[type="tel"], input[type="email"], input[type="password"] {
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
        <h2>Inscription</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="inscription.php" onsubmit="return validateForm()">
            <div>
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div>
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div>
                <label for="telephone">Téléphone</label>
                <input type="tel" id="telephone" name="telephone">
            </div>
            <div>
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div>
                <label for="mdp">Mot de passe *</label>
                <input type="password" id="mdp" name="mdp" required>
            </div>
            <button type="submit">S’inscrire</button>
        </form>
    </div>

    <script>
    function validateForm() {
        var mdp = document.getElementById('mdp').value;
        if (/\s/.test(mdp)) {
            alert('Le mot de passe ne doit pas contenir d\'espaces.');
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
