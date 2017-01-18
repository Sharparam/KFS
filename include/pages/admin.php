<?php
if (!$isAdmin) {
  header('Location: /');
  exit();
}
?>

<h1>Site Administration</h1>
