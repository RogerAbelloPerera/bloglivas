<?php
session_start();
require_once('../DBConnection.php');

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit();
}

$missatge = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = trim($_POST['text'] ?? '');
    $iduser = $_SESSION['iduser'];
    $imatge = null;

    if (empty($text)) {
        $missatge = "El text no pot estar buit.";
    } else {
        // Si hi ha imatge
        if (!empty($_FILES['imatge']['name'])) {
            $nomFitxer = basename($_FILES['imatge']['name']);
            $nomTemporal = time() . '_' . $nomFitxer;
            $rutaFitxer = "../uploads/" . $nomTemporal;

            if (move_uploaded_file($_FILES['imatge']['tmp_name'], $rutaFitxer)) {
                $imatge = "uploads/" . $nomTemporal;
            } else {
                $missatge = "No s'ha pogut pujar la imatge.";
            }
        }

        // INSERT
        try {
            $stmt = $db->prepare("INSERT INTO publicacio (usuari_id, text, imatge) VALUES (?, ?, ?)");
            $stmt->execute([$iduser, $text, $imatge]);

            // Redirecció només si INSERT correcte
            header("Location: home.php");
            exit();
        } catch (PDOException $e) {
            $missatge = "Error al crear la publicació: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Crear Publicació</title>
</head>
<body>
    <h2>Nova publicació</h2>

    <?php if (!empty($missatge)) echo "<p style='color:red;'>$missatge</p>"; ?>

    <form method="POST" action="createPost.php" enctype="multipart/form-data">
        <label for="text">Text:</label><br>
        <textarea name="text" rows="5" cols="50" placeholder="Escriu la teva publicació..." required></textarea><br><br>

        <label for="imatge">Imatge (opcional):</label><br>
        <input type="file" name="imatge"><br><br>

        <input type="submit" value="Publicar">
    </form>

    <br>
    <a href="home.php">Tornar a l'inici</a>
</body>
</html>
