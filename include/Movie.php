<?php
class Movie {
  private $id;
  private $title;
  private $original;
  private $genre;
  private $country;
  private $director;
  private $year;
  private $length;
  private $imdb;
  private $image;
  private $date;
  private $description;
  private $rating;

  public static function findById($id) {
    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'length, imdb, image, date, description, rating'
      . ' FROM movies WHERE id=:id LIMIT 1;';

    $stmt = Database::getInstance()->prepare($query);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    if ($stmt->rowCount() !== 1)
      return NULL;

    return $stmt->fetchObject('Movie');
  }

  public static function getAll() {
    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'length, imdb, image, date, description, rating'
      . ' FROM movies ORDER BY date DESC';

    $stmt = Database::getInstance()->query($query);

    return $stmt->fetchAll(PDO::FETCH_CLASS, 'Movie');
  }

  public static function getActive() {
    $query = "SELECT value FROM kfs_data WHERE key = 'season_start';";

    $db = Database::getInstance();

    $stmt = $db->query($query);
    $data = $stmt->fetch();
    $date = $data->value;

    $query = 'SELECT id, title, original, genre, country, director, year,'
      . 'length, imdb, image, date, description, rating'
      . ' FROM movies WHERE date > :date ORDER BY date ASC;';

    $stmt = $db->prepare($query);
    $stmt->bindValue(':date', $date);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, 'Movie');
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

  public function getLength() {
    $hours = floor($this->length / 60);
    $minutes = round(($this->length - $hours * 60) / 60);
    $hrSuffix = $hours == 1 ? 'e' : 'ar';
    $minSuffix = $minutes == 1 ? '' : 'er';

    if ($hours == 0)
      return "{$minutes} minut{$minSuffix}";
    else
      return "{$hours} timm{$hrSuffix} {$minutes} minut{$minSuffix}";
  }

  public function getImdb() {
    return $this->imdb;
  }

  public function getImage() {
    return $this->image;
  }

  public function getDate() {
    $ts = strtotime($this->date);
    setlocale(LC_TIME, 'sv_SE');
    return strftime('%e %B', $ts);
  }

  public function getDescription() {
    return $this->description;
  }

  public function getRating() {
    return round($this->rating, 1);
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

  public function setLength($length) {
    $this->length = $length;
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
    $this->rating = $rating;
  }

  public function save() {
    if ($this->id === NULL) {
      $this->insert();
      return;
    }

    $query = 'UPDATE movies'
      . 'SET title=:title, original=:original, genre=:genre, country=:country,'
      . 'director=:director, year=:year, length=:length, imdb=:imdb,'
      . 'image=:image, date=:date, description=:description, rating=:rating'
      . 'WHERE id=:id;';

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
      $stmt->bindValue(':length', $this->length);
      $stmt->bindValue(':imdb', $this->imdb);
      $stmt->bindValue(':image', $this->image);
      $stmt->bindValue(':date', $this->date);
      $stmt->bindValue(':description', $this->description);
      $stmt->bindValue(':rating', $this->rating);
      $stmt->bindValue(':id', $this->id);
      $stmt->execute();
      $db->commit();
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to update movie details: {$e->getMessage()}");
    }
  }

  public function toForm() {
    ?>
    <form action="<?= $_SERVER['REQUEST_URI'] ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
      <?php if ($this->getId() !== NULL): ?>
      <input type="hidden" id="id" name="id" value="<?= $this->getId() ?>">
      <?php endif; ?>

      <?php
      static::printTextField('title', 'Title', $this->getTitle());
      static::printTextField('original', 'Original', $this->getOriginal());
      static::printTextField('genre', 'Genre', $this->getGenre());
      static::printTextField('country', 'Country', $this->getCountry());
      static::printTextField('director', 'Director', $this->getDirector());
      static::printTextField('year', 'Year', $this->getYear());
      static::printTextField('length', 'Length', $this->length);
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
          <button type="submit" name="submit" id="submit" class="form-control btn btn-primary">
            Save
          </button>
        </div>
      </div>
    </form>
    <?php
  }

  public function __toString() {
    ?>
    <div class="movie">
      <h3><strong><?= $this->getDate() ?></strong></h3>
      <div class="media">
        <div class="media-left">
          <a href="http://www.imdb.com/title/<?= $this->getImdb() ?>/" title="Läs om <?= $this->getTitle() ?> på IMDb!">
            <img class="media-object thumbnail" src="images/movies/<?= $this->getImage() ?>" alt="<?= $this->getTitle() ?>">
          </a>
        </div>
        <div class="media-body">
          <h4 class="media-heading"><?= $this->getTitle() ?> <span>(<?= $this->getYear() ?>)</span></h4>
          <p><strong>Originaltitel: </strong><?= $this->getOriginal ?>.</p>
          <p><strong>Regissör: </strong><?= $this->getDirector() ?>.</p>
          <p><strong>Längd: </strong><?= $this->getLength() ?>.</p>
          <p><strong>Land: </strong><?= $this->getCountry() ?>.</p>
          <p><strong>Genre: </strong><?= $this->getGenre() ?>.</p>
          <p><?= $this->getDescription() ?></p>
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
      . '(title, original, genre, country, director, year, length, imdb, image,'
      . 'date, description, rating)'
      . 'VALUES(:title, :original, :genre, :country, :director, :year, :length,'
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
      $stmt->bindValue(':length', $this->length);
      $stmt->bindValue(':imdb', $this->imdb);
      $stmt->bindValue(':image', $this->image);
      $stmt->bindValue(':date', $this->date);
      $stmt->bindValue(':description', $this->description);
      $stmt->bindValue(':rating', $this->rating);
      $stmt->execute();
      $this->id = $db->lastInsertId();
      $db->commit();
    } catch (PDOException $e) {
      $db->rollBack();
      Alerts::addError("Failed to insert new movie to database: {$e->getMessage()}");
    }
  }
}
