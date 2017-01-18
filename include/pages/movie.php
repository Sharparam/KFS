<?php
use KFS\Alerts;
use KFS\Movie;

requireAdmin();

function setFromPost(&$movie) {
  $movie->setTitle($_POST['title']);
  $movie->setOriginal($_POST['original']);
  $movie->setGenre($_POST['genre']);
  $movie->setCountry($_POST['country']);
  $movie->setDirector($_POST['director']);
  $movie->setYear($_POST['year']);
  $movie->setDuration($_POST['duration']);
  $movie->setImdb($_POST['imdb']);
  $movie->setDate($_POST['date']);
  $movie->setDescription($_POST['description']);
  $movie->setRating($_POST['rating']);
}

if (!isset($_POST['update']) && isset($_GET['id'])) {
  $movie = Movie::findById($_GET['id']);
} elseif (isset($_POST['update']) && isset($_POST['id'])) {
  // Editing movie
  $movie = Movie::findById($_POST['id']);

  if ($movie === NULL) {
    Alerts::addError('Unable to find movie for editing!');
  } else {
    setFromPost($movie);

    if ($movie->validate()) {
      $status = $_FILES['image']['error'];
      $image = $_FILES['image']['name'];
      $tmp = $_FILES['image']['tmp_name'];

      switch ($status) {
        case UPLOAD_ERR_OK:
          if (file_exists(Movie::IMAGE_PATH . $image)) {
            Alerts::addError('Image already exists on server!');
          } else {
            $result = move_uploaded_file($tmp, Movie::IMAGE_PATH . $image);
            if ($result) {
              unlink(Movie::IMAGE_PATH . $movie->getImage());
              $movie->setImage($image);
            } else {
              Alerts::addError('Failed to move image to upload dir.');
            }
          }
          break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
          Alerts::addError('Image too large!');
          break;
        case UPLOAD_ERR_NO_FILE:
          // Do nothing
          break;
        default:
          Alerts::addError("Unknown file error: {$status}");
          break;
      }

      if (!Alerts::hasAlerts() && $movie->save()) {
        header('Location: /?p=admin');
        exit();
      }
    }
  }
} elseif (isset($_POST['create'])) {
  $movie = new Movie();
  setFromPost($movie);

  if ($movie->validate()) {
    $status = $_FILES['image']['error'];
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    switch ($status) {
      case UPLOAD_ERR_OK:
        if (file_exists(Movie::IMAGE_PATH . $image)) {
          Alerts::addError('Image already exists on server!');
        } else {
          $result = move_uploaded_file($tmp, Movie::IMAGE_PATH . $image);
          if ($result)
            $movie->setImage($image);
          else
            Alerts::addError('Failed to move image to upload dir.');
        }
        break;
      case UPLOAD_ERR_INI_SIZE:
      case UPLOAD_ERR_FORM_SIZE:
        Alerts::addError('Image too large!');
        break;
      case UPLOAD_ERR_NO_FILE:
        Alerts::addError('Missing image!');
        break;
      default:
        Alerts::addError("Unknown file error: {$status}");
        break;
    }

    if (!Alerts::hasAlerts() && $movie->save()) {
      header('Location: /?p=admin');
      exit();
    }
  }
} else {
  $movie = new Movie();
}
?>

<?php if (Alerts::hasAlerts()) Alerts::printAll(); ?>

<?php $movie->printForm(); ?>

<?php $scripts[] = '/js/movie.js';
