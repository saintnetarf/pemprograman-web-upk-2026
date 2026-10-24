<?php
$nama = "Nama Anda";
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Halaman Dinamis</title>
</head>
<body>
  <h1>Halo, <?= $nama ?>!</h1>
  <p>Waktu server: <?= date("d-m-Y H:i:s") ?></p>
  <p>Versi PHP: <?= phpversion() ?></p>
</body>
</html>
