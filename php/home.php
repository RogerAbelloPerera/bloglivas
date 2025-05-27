<?php
session_start();
require_once('functions.php');
require_once('../DBConnection.php');

if (!isUserLoggedIn()) {
    header("Location: index.html");
    exit;
}

$nom = $_SESSION['userFirstName'] ?? '';
$usuari = $_SESSION['username'] ?? '';

// Obtenir publicacions amb info d’usuari, nombre de comentaris i reaccions
$stmt = $db->prepare("
    SELECT 
      p.*,
      u.username,
      u.profile_image,
      -- Comptem només els likes
      (SELECT COUNT(*) 
         FROM reaccio r 
        WHERE r.publicacio_id = p.id 
          AND r.tipus = 'like'
      ) AS total_likes,
      -- Comptem només els dislikes
      (SELECT COUNT(*) 
         FROM reaccio r 
        WHERE r.publicacio_id = p.id 
          AND r.tipus = 'dislike'
      ) AS total_dislikes,
      -- Comptem els comentaris
      (SELECT COUNT(*) 
         FROM comentari c 
        WHERE c.publicacio_id = p.id
      ) AS total_comentaris
    FROM publicacio p
    JOIN users u ON p.usuari_id = u.iduser
    ORDER BY p.data_publicacio DESC
");
$stmt->execute();
$publicacions = $stmt->fetchAll();

$stmt->execute();
$publicacions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Benvingut</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #2e4d2c; /* verd fosc mitjà */
            color: white;
        }

        .contenidor {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 600px;
            border-radius: 15px;
            text-align: center;
        }

        .boton-logout {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #ff5555;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        .boton-logout:hover {
            background-color: #ff2222;
        }

        .post {
            background-color: rgba(255, 255, 255, 0.1);
            margin: 2rem auto;
            padding: 1rem;
            max-width: 600px;
            border-radius: 10px;
        }

        .post img {
            max-width: 100%;
            border-radius: 5px;
        }

        .autor {
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .autor img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .data {
            font-size: 0.85em;
            color: #ccc;
        }

        .interaccions {
            margin-top: 10px;
            font-size: 0.9em;
        }

        .interaccions span {
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="contenidor">
        <img src="../assets/logo.png" alt="bloglivas">
        <h1>Benvingut<?php echo $nom ? ", $nom" : ", $usuari"; ?>!</h1>
        <p>Estàs connectat a bloglivas!!!</p>
        <form action="../php/perfil.php" method="POST">
            <button type="submit" class="boton-logout">Perfil</button>
        </form>
        <form action="logout.php" method="POST">
            <button type="submit" class="boton-logout">Tancar sessió</button>
        </form>
        <form action="createPost.php" method="GET">
            <button type="submit" class="boton-logout">Crear Post</button>
        </form>
    </div>

<?php foreach ($publicacions as $pub): ?>
    <div class="post">
        <!-- Autor i imatge de perfil -->
        <div class="autor">
            <a href="perfil.php?id=<?= $pub['usuari_id'] ?>" style="text-decoration:none; color:inherit;">
            <?php if ($pub['profile_image']): ?>
                <img src="../<?= htmlspecialchars($pub['profile_image']) ?>"
                    alt="Foto de perfil"
                    style="width:30px; height:30px; border-radius:50%; object-fit:cover; margin-right:8px;">
            <?php endif; ?>
            <?= htmlspecialchars($pub['username']) ?>
            </a>
        </div>


        <!-- Data -->
        <div class="data">
            <?= date('d/m/Y H:i', strtotime($pub['data_publicacio'])) ?>
        </div>

        <!-- Text i imatge de la publicació -->
        <p><?= nl2br(htmlspecialchars($pub['text'])) ?></p>
        <?php if ($pub['imatge']): ?>
            <img src="../<?= htmlspecialchars($pub['imatge']) ?>"
                 alt="imatge publicació"
                 style="max-width:100%; margin-top:10px; border-radius:5px;">
        <?php endif; ?>

        <!-- Reaccions i puntuació -->
        <div class="interaccions" style="margin-top:10px;">
            <!-- Like -->
            <form style="display:inline" method="POST" action="react.php">
                <input type="hidden" name="post_id" value="<?= $pub['id'] ?>">
                <input type="hidden" name="type" value="like">
                <button type="submit">❤️ <?= $pub['total_likes'] ?></button>
            </form>
            <!-- Dislike -->
            <form style="display:inline" method="POST" action="react.php">
                <input type="hidden" name="post_id" value="<?= $pub['id'] ?>">
                <input type="hidden" name="type" value="dislike">
                <button type="submit">👎 <?= $pub['total_dislikes'] ?></button>
            </form>
            <!-- Comentaris -->
            <span style="margin-left:15px;">💬 <?= $pub['total_comentaris'] ?></span>
            <!-- Puntuació total -->
            <span style="margin-left:15px;">
                📊 <?= $pub['total_likes'] + $pub['total_dislikes'] + $pub['total_comentaris'] ?>
            </span>
        </div>


        <!-- Formulari de comentari -->
        <div class="comentar" style="margin-top:15px;">
            <form method="POST" action="comment.php">
                <input type="hidden" name="post_id" value="<?= $pub['id'] ?>">
                <textarea name="comment_text" rows="2"
                          style="width:100%; padding:8px; border-radius:4px; border:1px solid #ccc;"
                          placeholder="Escriu un comentari..." required></textarea>
                <button type="submit"
                        style="margin-top:5px; padding:6px 12px; border:none; border-radius:4px;
                               background-color:#ffc107; cursor:pointer;">
                    Comentar
                </button>
            </form>
        </div>

        <!-- Llista de comentaris -->
        <div class="comments-list" style="margin-top:15px;">
            <?php
            $stmtC = $db->prepare("
                SELECT c.text, c.data_comentari, u.username, u.profile_image
                FROM comentari c
                JOIN users u ON c.usuari_id = u.iduser
                WHERE c.publicacio_id = ?
                ORDER BY c.data_comentari ASC
            ");
            $stmtC->execute([$pub['id']]);
            $comments = $stmtC->fetchAll();
            ?>
            <?php foreach ($comments as $c): ?>
                <div class="comment" style="display:flex; gap:8px; margin-bottom:10px; align-items:flex-start;">
                    <?php if ($c['profile_image']): ?>
                        <img src="../<?= htmlspecialchars($c['profile_image']) ?>"
                             alt="avatar"
                             style="width:24px; height:24px; border-radius:50%; object-fit:cover;">
                    <?php endif; ?>
                    <div>
                        <strong><?= htmlspecialchars($c['username']) ?></strong>
                        <span style="font-size:0.75em; color:#ccc; margin-left:4px;">
                            <?= date('d/m/Y H:i', strtotime($c['data_comentari'])) ?>
                        </span>
                        <p style="margin:4px 0;"><?= nl2br(htmlspecialchars($c['text'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endforeach; ?>

</body>
</html>
