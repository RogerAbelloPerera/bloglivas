<?php
session_start();
require_once('functions.php');
require_once('../DBConnection.php');

if (!isUserLoggedIn()) {
    header("Location: index.html");
    exit;
}

// Agafem l'id per GET; si no n'hi ha, redirigim al nostre perfil
$viewId = isset($_GET['id']) ? intval($_GET['id']) : $_SESSION['iduser'];

// Recollim dades de l'usuari
$stmtU = $db->prepare("SELECT username, userFirstName, userLastName, biography, profile_image, banner_image FROM users WHERE iduser = ?");
$stmtU->execute([$viewId]);
$user = $stmtU->fetch();
if (!$user) {
    die("Usuari no trobat.");
}

// Recollim les publicacions d’aquest usuari
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

$stmtP->execute([$viewId]);
$posts = $stmtP->fetchAll();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Perfil de <?= htmlspecialchars($user['username']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
      /* El teu CSS de perfil: banner, avatar, post, etc. */
      .banner { width:100%; height:200px; background-size:cover; background-position:center; }
      .avatar { width:100px; height:100px; border-radius:50%; border:3px solid white; margin-top:-50px; }
      .post { background:white; max-width:600px; margin:1rem auto; padding:1rem; border-radius:8px; box-shadow:0 2px 4px rgba(0,0,0,0.1); }
      .meta { font-size:0.85em; color:#666; }
      .stats { font-size:0.9em; margin-top:0.5rem; }
      .stats span { margin-right:1rem; }
    </style>
</head>
<body>

  <!-- Banner -->
  <?php if ($user['banner_image']): ?>
    <div class="banner" style="background-image:url('../<?= htmlspecialchars($user['banner_image']) ?>');"></div>
  <?php endif; ?>

  <!-- Header perfil -->
  <div style="text-align:center; background:#007bff; color:white; padding:2rem;">
    <?php if ($user['profile_image']): ?>
      <img src="../<?= htmlspecialchars($user['profile_image']) ?>" alt="Avatar" class="avatar">
    <?php endif; ?>
    <h1><?= htmlspecialchars("{$user['userFirstName']} {$user['userLastName']}") ?> (@<?= htmlspecialchars($user['username']) ?>)</h1>
    <?php if ($user['biography']): ?>
      <p style="max-width:600px; margin:0.5rem auto; color:white;"><?= nl2br(htmlspecialchars($user['biography'])) ?></p>
    <?php endif; ?>
  </div>

  <!-- Publicacions -->
  <?php foreach ($posts as $p): ?>
    <div class="post">
      <div class="meta">
        Publicada el <?= date('d/m/Y H:i', strtotime($p['data_publicacio'])) ?>
      </div>
      <p><?= nl2br(htmlspecialchars($p['text'])) ?></p>
      <?php if ($p['imatge']): ?>
        <img src="../<?= htmlspecialchars($p['imatge']) ?>" alt="Imatge publicació" style="max-width:100%; margin-top:0.5rem; border-radius:5px;">
      <?php endif; ?>
      <div class="stats">
        <span>❤️ <?= $p['total_reaccions'] ?></span>
        <span>💬 <?= $p['total_comentaris'] ?></span>
        <span>📊 <?= $p['total_reaccions'] + $p['total_comentaris'] ?></span>
      </div>
    </div>
  <?php endforeach; ?>

</body>
</html>
