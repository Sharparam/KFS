<?php
use KFS\Alerts;
use KFS\Database;
use KFS\Movie;

requireAdmin();

$page = 0;
$pages = Movie::getPageCount();

if (isset($_GET['page']) && !empty($_GET['page']))
  $page = intval($_GET['page']);

if (isset($_POST['delete'])) {
  $movie = Movie::findById($_POST['id']);
  if ($movie !== NULL && $movie->delete())
    Alerts::addSuccess('Movie delete successfully!');
  else
    Alerts::addError('Failed to delete movie!');
}

if (isset($_POST['update'])) {
  $year = $_POST['year'];
  $season = $_POST['season'];
  $season_start = $_POST['season_start'];

  foreach (array('year', 'season', 'season_start') as $key) {
    if (!empty($_POST[$key])) {
      $query = 'UPDATE data SET value = :value WHERE `key` = :key;';
      $stmt = Database::getInstance()->prepare($query);
      $stmt->bindValue(':value', $_POST[$key]);
      $stmt->bindValue(':key', $key);
      $stmt->execute();
    }
  }
}

$movies = Movie::getPaged($page);

$query = "SELECT `key`, value FROM data WHERE `key` IN ('year', 'season', 'season_start');";
$stmt = Database::getInstance()->query($query);

while ($entry = $stmt->fetch()) {
  switch ($entry->key) {
    case 'year':
      $year = $entry->value;
      break;
    case 'season':
      $season = $entry->value;
      break;
    case 'season_start':
      $season_start = $entry->value;
      break;
  }
}

if (Alerts::hasAlerts())
  Alerts::printAll();
?>

<h1>Site Administration</h1>
<h2>Data</h2>
<form class="form-inline">
  <div class="form-group">
    <label for="year">Year</label>
    <input type="text" name="year" id="year" class="form-control" placeholder="Year" value="<?= $year ?>">
  </div>
  <div class="form-group">
    <label for="season">Season</label>
    <input type="text" name="season" id="season" class="form-control" placeholder="Season" value="<?= $season ?>">
  </div>
  <div class="form-group">
    <label for="season_start">Season start</label>
    <input type="text" name="season_start" id="season_start" class="form-control" placeholder="Season start" value="<?= $season_start ?>">
  </div>
  <button type="submit" name="update" class="btn btn-primary">Update</button>
</form>
<h2>Movies</h2>
<p><a href="?p=movie" class="btn btn-success">Create</a></p>
<?php if (empty($movies)): ?>
<p><em>There are no movies!</em></p>
<?php else: ?>
<table class="table table-striped">
  <thead>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Date</th>
      <th>Rating</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($movies as $movie): ?>
    <tr>
      <td><?= $movie->getId() ?></td>
      <td><?= $movie->getTitle() ?></td>
      <td><?= $movie->getDate() ?></td>
      <td><?= $movie->getRating() ?? '<em>None</em>'; ?></td>
      <td class="pull-right">
        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-inline" style="display: inline;">
          <input type="hidden" name="season_start" value="<?= $movie->getDate() ?>">
          <button type="submit" name="update" class="btn btn-success btn-sm">Set as season start</button>
        </form>
        <a href="/?p=movie&amp;id=<?= $movie->getId() ?>" class="btn btn-default btn-sm">Edit</a>
        <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-inline" style="display: inline;"
          onsubmit="return window.confirm('Are you sure?');">
          <input type="hidden" name="id" value="<?= $movie->getId() ?>">
          <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="center-block text-center">
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <li <?php if ($page == 0) echo 'class="disabled"' ?> aria-label="Previous">
        <a href="?p=admin&amp;page=<?= $page == 0 ? 0 : $page - 1 ?>">&laquo;</a>
      </li>
    <?php for ($p = 0; $p < $pages; $p++): ?>
      <li <?php if ($p == $page) echo 'class="active"'; ?>>
        <a href="?p=admin&amp;page=<?= $p ?>"><?= $p + 1 ?></a>
      </li>
    <?php endfor; ?>
      <li <?php if ($page == $pages - 1) echo 'class="disabled"' ?> aria-label="Next">
        <a href="?p=admin&amp;page=<?= $page == $pages - 1 ? $page : $page + 1 ?>">&raquo;</a>
      </li>
    </ul>
  </nav>
</div>
<?php endif; ?>
