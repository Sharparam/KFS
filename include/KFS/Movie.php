<?php
namespace KFS;

use \PDO;
use \PDOException;

class Movie {
  const DEFAULT_PAGE_SIZE = 10;

  const IMAGE_PATH = Config::PUBLIC_HTML . '/images/movies/';

  private $id;
  private $title;
  private $original;
  private $genre;
  private $country;
  private $director;
  private $year;
  private $duration;
  private $imdb;
  private $image;
  private $date;
  private $description;
  private $rating;

  public static function findById($id) {
    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'duration, imdb, image, date, description, rating'
      . ' FROM movies WHERE id=:id LIMIT 1;';

    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() !== 1)
      return NULL;

    return $stmt->fetchObject('KFS\\Movie');
  }

  public static function getAll() {
    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'duration, imdb, image, date, description, rating'
      . ' FROM movies ORDER BY date DESC';

    $stmt = Database::getInstance()->query($query);

    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Movie');
  }

  public static function getActive() {
    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'duration, imdb, image, date, description, rating'
      . ' FROM movies WHERE date >= :date ORDER BY date ASC;';

    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':date', Data::getSeasonStart());
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Movie');
  }

  public static function getUpcoming() {
    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'duration, imdb, image, date, description, rating'
      . ' FROM movies WHERE date >= CURDATE() ORDER BY date ASC;';

    $stmt = Database::getInstance()->query($query);

    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Movie');
  }

  public static function getRated() {
    $query = 'SELECT title, date, rating FROM movies
      WHERE rating IS NOT NULL AND rating > 0
      ORDER BY date ASC;';

    $stmt = Database::getInstance()->query($query);
    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Movie');
  }

  public static function getPaged($page = 0, $size = self::DEFAULT_PAGE_SIZE) {
    $query = 'SELECT id, title, original, genre, country, director, year,
      duration, imdb, image, date, description, rating
      FROM movies ORDER BY date DESC LIMIT :limit OFFSET :start;';

    $db = Database::getInstance();

    $stmt = $db->prepare($query);
    $stmt->bindValue(':start', $page * $size, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $size, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, 'KFS\\Movie');
  }

  public static function getPageCount($size = self::DEFAULT_PAGE_SIZE) {
    $query = 'SELECT CEIL(COUNT(*) / :size) as pages FROM movies;';
    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':size', $size);
    $stmt->execute();
    return (int)$stmt->fetch()->pages;
  }

  public static function averageRating() {
    $query = 'SELECT AVG(rating) AS avg FROM movies WHERE rating IS NOT NULL AND rating > 0;';
    $stmt = Database::getInstance()->query($query);
    return round($stmt->fetch()->avg, 1);
  }

  public function getId() {
    return $this->id;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getOriginal() {
    return $this->original;
  }

  public function getGenre() {
    return $this->genre;
  }

  public function getCountry() {
    return $this->country;
  }

  public function getDirector() {
    return $this->director;
  }

  public function getYear() {
    return $this->year;
  }

  public function getDuration() {
    return $this->duration;
  }

  public function getImdb() {
    return $this->imdb;
  }

  public function getImage() {
    return $this->image;
  }

  public function getDate() {
    return $this->date;
  }

  public function getDescription() {
    return $this->description;
  }

  public function getRating() {
    return $this->rating;
  }

  public function setTitle($title) {
    $this->title = $title;
  }

  public function setOriginal($original) {
    $this->original = $original;
  }

  public function setGenre($genre) {
    $this->genre = $genre;
  }

  public function setCountry($country) {
    $this->country = $country;
  }

  public function setDirector($director) {
    $this->director = $director;
  }

  public function setYear($year) {
    $this->year = $year;
  }

  public function setDuration($duration) {
    $this->duration = $duration;
  }

  public function setImdb($imdb) {
    $this->imdb = $imdb;
  }

  public function setImage($image) {
    $this->image = $image;
  }

  public function setDate($date) {
    $this->date = $date;
  }

  public function setDescription($description) {
    $this->description = $description;
  }

  public function setRating($rating) {
    if (empty($rating) || $rating == 0)
      $this->rating = NULL;
    else
      $this->rating = $rating;
  }

  public function formatDuration() {
    $hours = floor($this->duration / 60);
    $minutes = $this->duration - $hours * 60;
    $hrSuffix = $hours == 1 ? 'e' : 'ar';
    $minSuffix = $minutes == 1 ? '' : 'er';

    if ($hours == 0)
      return "{$minutes} minut{$minSuffix}";
    elseif ($minutes == 0)
      return "{$hours} timm{$hrSuffix}";
    else
      return "{$hours} timm{$hrSuffix} {$minutes} minut{$minSuffix}";
  }

  public function formatDate() {
    $ts = strtotime($this->date);
    setlocale(LC_TIME, 'sv_SE');
    return strftime('%e %B', $ts);
  }

  public function formatRating() {
    return round($this->rating, 1);
  }

  public function validate() {
    if (empty($this->title))
      Alerts::addError('Title cannot be empty!');

    if (empty($this->original))
      Alerts::addError('Original title cannot be empty!');

    if (empty($this->genre))
      Alerts::addError('Genre cannot be empty!');

    if (empty($this->country))
      Alerts::addError('Country cannot be empty!');

    if (empty($this->director))
      Alerts::addError('Director cannot be empty!');

    if (empty($this->year) || !preg_match("/^\d{4}$/", $this->year))
      Alerts::addError('Invalid year!');

    if (empty($this->duration) || !preg_match("/^\d+$/", $this->duration))
      Alerts::addError('Invalid duration!');

    if (empty($this->imdb))
      Alerts::addError('IMDb link cannot be empty!');

    if (empty($this->date) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $this->date))
      Alerts::addError('Invalid date!');

    if (empty($this->description))
      Alerts::addError('Description cannot be empty!');

    return !Alerts::hasAlerts();
  }

  public function up() {

  }

  public function save() {
    if ($this->id === NULL) {
      return $this->insert();
    }

    $query = 'UPDATE movies
      SET title=:title, original=:original, genre=:genre, country=:country,
      director=:director, year=:year, duration=:duration, imdb=:imdb,
      image=:image, date=:date, description=:description, rating=:rating
      WHERE id=:id;';

    $db = Database::getInstance();

    try {
      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->bindValue(':title', $this->title);
      $stmt->bindValue(':original', $this->original);
      $stmt->bindValue(':genre', $this->genre);
      $stmt->bindValue(':country', $this->country);
      $stmt->bindValue(':director', $this->director);
      $stmt->bindValue(':year', $this->year);
      $stmt->bindValue(':duration', $this->duration);
      $stmt->bindValue(':imdb', $this->imdb);
      $stmt->bindValue(':image', $this->image);
      $stmt->bindValue(':date', $this->date);
      $stmt->bindValue(':description', $this->description);
      $stmt->bindValue(':rating', $this->rating);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      $db->commit();
      return true;
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to update movie details: {$e->getMessage()}");
      return false;
    }
  }

  public function delete() {
    if ($this->getId() === NULL)
      return true;

    try {
      $query = 'DELETE FROM movies WHERE id = :id;';
      $stmt = Database::getInstance()->prepare($query);
      $stmt->bindValue(':id', $this->getId());
      $stmt->execute();

      if ($stmt->rowCount() > 0)
        unlink(self::IMAGE_PATH . $this->getImage());

      return true;
    } catch (PDOException $e) {
      Alerts::addError("Failed to delete movie: {$e->getMessage()}");
      return false;
    }
  }

  public function printForm($action = NULL) {
    if ($action === NULL)
      $action = $_SERVER['REQUEST_URI'];
    ?>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
      <?php if ($this->getId() !== NULL): ?>
      <input type="hidden" id="id" name="id" value="<?= $this->getId() ?>">
      <?php endif; ?>

      <div class="form-group">
        <label for="tmdb" class="col-sm-2 control-label"><a href="https://www.themoviedb.org/">TMDb</a> search</label>
        <div class="col-sm-10">
          <select id="tmdb" class="form-control"><option></option></select>
        </div>
      </div>

      <?php
      static::printTextField('title', 'Title', $this->getTitle());
      static::printTextField('original', 'Original', $this->getOriginal());
      static::printTextField('genre', 'Genre', $this->getGenre());
      static::printTextField('country', 'Country', $this->getCountry());
      static::printTextField('director', 'Director', $this->getDirector());
      static::printTextField('year', 'Year', $this->getYear());
      static::printTextField('duration', 'Duration', $this->duration);
      static::printTextField('imdb', 'IMDb', $this->getImdb());
      static::printTextField('date', 'Date', $this->date);
      static::printTextField('rating', 'Rating', $this->getRating());
      ?>

      <div class="form-group">
        <label for="image" class="col-sm-2 control-label">Image</label>
        <div class="col-sm-10">
          <input type="file" name="image" id="image" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label for="description" class="col-sm-2 control-label">Description</label>
        <div class="col-sm-10">
          <textarea name="description" id="description" wrap="soft" class="form-control" placeholder="Description"><?= $this->getDescription() ?></textarea>
        </div>
      </div>

      <div class="form-group">
        <label for="submit" class="col-sm-2 control-label">Done?</label>
        <div class="col-sm-10">
          <button type="submit" name="<?= $this->getId() === NULL ? 'create' : 'update' ?>" class="form-control btn btn-primary">
            Save
          </button>
        </div>
      </div>
    </form>
    <?php
  }

  public function display() {
    ?>
    <div class="movie">
      <h3><strong><?= $this->formatDate() ?></strong></h3>
      <div class="media">
        <div class="media-left">
          <a href="<?= $this->getImdb() ?>" title="Läs om <?= $this->getTitle() ?> på IMDb!">
            <img class="media-object thumbnail" src="/images/movies/<?= $this->getImage() ?>" alt="<?= $this->getTitle() ?>">
          </a>
        </div>
        <div class="media-body">
          <h4 class="media-heading"><?= $this->getTitle() ?> <span>(<?= $this->getYear() ?>)</span></h4>
          <p><strong>Originaltitel: </strong><?= $this->getOriginal() ?>.</p>
          <p><strong>Regissör: </strong><?= $this->getDirector() ?>.</p>
          <p><strong>Längd: </strong><?= $this->formatDuration() ?>.</p>
          <p><strong>Land: </strong><?= $this->getCountry() ?>.</p>
          <p><strong>Genre: </strong><?= $this->getGenre() ?>.</p>
          <?= Markdown::parse($this->getDescription()) ?>
        </div>
      </div>
    </div>
    <?php
  }

  private static function printTextField($name, $text, $value) {
    ?>
    <div class="form-group">
      <label for="<?= $name ?>" class="col-sm-2 control-label"><?= $text ?></label>

      <div class="col-sm-10">
        <input type="text" name="<?= $name ?>" id="<?= $name ?>" placeholder="<?= $text ?>" class="form-control" value="<?= $value ?>">
      </div>
    </div>
    <?php
  }

  private function insert() {
    $query = 'INSERT INTO movies'
      . '(title, original, genre, country, director, year, duration, imdb, image,'
      . 'date, description, rating)'
      . 'VALUES(:title, :original, :genre, :country, :director, :year, :duration,'
      . ':imdb, :image, :date, :description, :rating);';

    $db = Database::getInstance();

    try {
      $db->beginTransaction();
      $stmt = $db->prepare($query);
      $stmt->bindValue(':title', $this->title);
      $stmt->bindValue(':original', $this->original);
      $stmt->bindValue(':genre', $this->genre);
      $stmt->bindValue(':country', $this->country);
      $stmt->bindValue(':director', $this->director);
      $stmt->bindValue(':year', $this->year);
      $stmt->bindValue(':duration', $this->duration);
      $stmt->bindValue(':imdb', $this->imdb);
      $stmt->bindValue(':image', $this->image);
      $stmt->bindValue(':date', $this->date);
      $stmt->bindValue(':description', $this->description);
      $stmt->bindValue(':rating', $this->rating);
      $stmt->execute();
      $this->id = $db->lastInsertId();
      $db->commit();
      return true;
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to insert new movie to database: {$e->getMessage()}");
      return false;
    }
  }
}
