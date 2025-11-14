<?php
// header.php
?>

<head>
  <link rel="stylesheet" href="css/header.css">
</head>

<div class="header">
    <h3><?= htmlspecialchars($_SESSION['user']['nama']) ?></h3>
    <div class="profile-icon">
      <i class="bi bi-person-fill"></i>
    </div>
</div>