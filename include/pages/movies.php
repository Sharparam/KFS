<?php use KFS\Movie; ?>
<h2 class="text-center">Filmer under <?= KFS\Data::getSeasonText() ?></h2>
<p><em>Klicka på bilderna för att besöka respektive films IMDb-sida!</em></p>
<p>Där inget annat anges körs två visningar för varje film, en på eftermiddagen klockan 15:30 och ytterligare en på
    kvällen klockan 18:15.</p>
<div class="movie-list">
    <?php
    $movies = Movie::getActive();
    if (empty($movies)) {
      ?> <h3><strong>Inga filmer just nu</strong></h3> <?php
    } else {
      foreach ($movies as $movie)
        $movie->display();
    }
    ?>
</div>
<?php
$rated = Movie::getRated();

if (!empty($rated)):

$count = count($rated);

$columns = 3;

$size = ceil($count / $columns);
?>
<h2>Filmbetyg</h2>
<p>På filmvisningar har våra medlemmar möjlighet att rösta på filmer som visats, resultaten av betygsättningen visas här (betygskalan är 1 till 5 där 5 är bäst).</p>
<p>Genomsnittsbetyget räknat över alla filmer är
  <?= number_format(Movie::averageRating(), 1, ',', '.') ?>.</p>
<div class="row">
<?php for ($col = 0; $col < $columns; $col++): ?>
  <div class="col-md-4">
    <table class="table table-condensed ratings">
      <thead><tr><th>Film</th><th>Datum</th><th>Betyg</th></tr></thead>
      <tbody>
      <?php
        for ($i = $size * $col; $i < $count && $i < $size * ($col + 1); $i++):
          $movie = $rated[$i];
      ?>
        <tr>
          <td><?= $movie->getTitle() ?></td>
          <td><?= date('Y-m-d', strtotime($movie->getDate())) ?></td>
          <td><?= number_format($movie->getRating(), 1, ',', '.') ?></td>
        </tr>
      <?php endfor; ?>
      </tbody>
    </table>
  </div>
<?php endfor; ?>
</div>
<?php endif; ?>
