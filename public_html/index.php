<?php
ob_start();

use KFS\Alerts;
use KFS\Data;
use KFS\User;
use KFS\Page;

session_start();
session_regenerate_id(true);

require_once('../include/init.php');

if (isset($_POST['login']) && User::login($_POST['username'], $_POST['password'])) {
  Alerts::addSuccess('Successfully logged in!');
} elseif (isset($_GET['a']) && $_GET['a'] === 'logout') {
  User::logout();
}

$pages = array(
    'movies' => 'pages/movies.php',
    'login' => 'pages/login.php',
    'register' => 'pages/register.php'
);

$isLoggedIn = User::isLoggedIn();
$user = User::getLoggedInUser();
$isAdmin = $user !== NULL && $user->isAdmin();

if ($isAdmin) {
  $pages['admin'] = 'pages/admin.php';
  $pages['movie'] = 'pages/movie.php';
  $pages['page'] = 'pages/page.php';
}

$page = $_GET['p'];
if ($page === NULL || $page === '')
    $page = 'home';
$file = NULL;
$err = NULL;

if (array_key_exists($page, $pages))
  $file = $pages[$page];
elseif (Page::isPage($page))
  $file = Page::findByName($page);

if ($file === NULL)
  $file = Page::findByName('home');

$scripts = array();
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="verify-v1" content="1mvRHK/U5l/D8/C9P/9qCLCZy36QuER/Dcm0QwnEC+E="/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="description"
        content="Välkommen till Kristinehamns filmstudio! Sidan för dig som gillar film och bor i Kristinehamn! KFS är en del av Sveriges Förenade Filmstudios"/>
  <meta name="keywords"
        content="kristinehamn, krhmn, kristinehamns, krhmns, kommun, kommuns, film, studio, filmstudio, sff, filmstudios, sveriges, förenade, filmstudios"/>
  <meta name="robots" content="index, follow"/>
  <meta name="author" content="Adam Hellberg"/>
  <meta name="language" content="Svenska, SV"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Kristinehamns Filmstudio</title>
  <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="css/main.css" rel="stylesheet" type="text/css"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
  <link href="favicon.ico" rel="shortcut icon"/>
</head>
<body>
<div class="container">
  <h1 id="page-header">Kristinehamns Filmstudio</h1>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">KFS</a>
      </div>
      <div id="sff-nav-logo" class="navbar-brand navbar-right">
        <a href="http://sff-filmstudios.org"><img src="images/logos/sff_78x41.png"></a>
      </div>
      <div id="navbar" class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
          <?php foreach (Page::getAllEnabled() as $page): ?>
          <li>
            <a href="/?p=<?= $page->getName() ?>" title="<?= $page->getTitle() ?>">
              <?= $page->getTitle() ?>
            </a>
          </li>
          <?php endforeach; ?>
          <li><a href="/?p=movies" title="Filmer som visas under <?= Data::getSeasonText() ?>">Filmer <?= Data::getYear() ?></a></li>
          <?php if ($isAdmin): ?><li><a href="/?p=admin">Admin</a></li><?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
  <?php
  if (Alerts::hasAlerts()) {
    Alerts::printAll();
  }

  if (is_string($file))
    include($file);
  elseif (get_class($file) === 'KFS\\Page')
    $file->render();
  else
    Alerts::printError('Okänt fel inträffade, kontakta webbutvecklaren med addressen till den här sidan.');
  ?>

  <footer class="clearfix">
    <hr>
    <p>
      Copyright &copy; 2010-2017 by Kristinehamns Filmstudio |
      <?php if ($isLoggedIn): ?>
      <?= $user ?> (<a href="/?a=logout">Logga ut</a>)
      <?php else: ?>
      <a href="/?p=login">Logga in</a>
      <?php endif; ?>
    </p>
  </footer>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<?php
if (!empty($scripts)) {
  foreach ($scripts as $script):
  ?>
<script src="<?= $script ?>"></script>
  <?php
  endforeach;
}
?>
</body>
</html>

<?php ob_end_flush(); ?>
