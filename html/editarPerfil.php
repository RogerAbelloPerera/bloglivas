<?php
session_start();
require_once('../DBConnection.php');

if (!isset($_SESSION['iduser'])) {
    header("Location: ../html/index.html");
    exit;
}

$iduser = $_SESSION['iduser'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $firstName = $_POST['userFirstName'];
    $lastName = $_POST['userLastName'];
    $biography = $_POST['biography'];
    $location = $_POST['location'];
    $age = intval($_POST['age']);

    $profileImage = $_SESSION['profile_image']; // valor actual
    if (!empty($_FILES['profile_image']['name'])) {
        $profileImageName = basename($_FILES['profile_image']['name']);
        $targetPath = "../uploads/" . $profileImageName;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetPath)) {
            $profileImage = "uploads/" . $profileImageName;
        }
    }
    
    $bannerImage = $_SESSION['banner_image']; // valor actual
    if (!empty($_FILES['banner_image']['name'])) {
        $bannerImageName = basename($_FILES['banner_image']['name']);
        $targetPath = "../uploads/" . $bannerImageName;
        if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
            $bannerImage = "uploads/" . $bannerImageName;
        }
    }
    

    $sql = "UPDATE users SET userFirstName = ?, userLastName = ?, biography = ?, location = ?, age = ?, profile_image = ?, banner_image = ? WHERE iduser = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$firstName, $lastName, $biography, $location, $age, $profileImage, $bannerImage, $iduser]);

    $_SESSION['userFirstName'] = $firstName;
    $_SESSION['userLastName'] = $lastName;
    $_SESSION['biography'] = $biography;
    $_SESSION['location'] = $location;
    $_SESSION['age'] = $age;
    $_SESSION['profile_image'] = $profileImage;
    $_SESSION['banner_image'] = $bannerImage;

    $successMessage = "Perfil actualitzat correctament.";
}

$stmt = $db->prepare("SELECT * FROM users WHERE iduser = ?");
$stmt->execute([$iduser]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 600px;
            background: white;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
        }

        input[type="submit"], .volver-btn {
            margin-top: 20px;
            width: 100%;
            background-color: #0073e6;
            color: white;
            border: none;
            padding: 12px;
            font-size: 1rem;
            border-radius: 6px;
            cursor: pointer;
        }

        .volver-btn {
            background-color: #555;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }

        .volver-btn:hover,
        input[type="submit"]:hover {
            background-color: #005bb5;
        }

        .success {
            text-align: center;
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Editar Perfil</h1>

    <?php if (isset($successMessage)): ?>
        <p class="success"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nom:</label>
        <input type="text" name="userFirstName" value="<?php echo htmlspecialchars($user['userFirstName']); ?>">

        <label>Cognom:</label>
        <input type="text" name="userLastName" value="<?php echo htmlspecialchars($user['userLastName']); ?>">

        <label>Biografia:</label>
        <textarea name="biography" rows="4"><?php echo htmlspecialchars($user['biography']); ?></textarea>

        <label>Ubicació:</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>">

        <label>Edat:</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>">

        <label>Imatge de perfil:</label>
        <input type="file" name="profile_image">

        <label>Imatge de banner:</label>
        <input type="file" name="banner_image">

        <input type="submit" name="update" value="Desar canvis">
    </form>

    <a href="../html/perfil.php" class="volver-btn">← Tornar al perfil</a>
</div>

</body>
</html>
