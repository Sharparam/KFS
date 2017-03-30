<?php
require_once('../include/init.php');
header('Content-Type: application/json');

try {
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $movie = KFS\Movie::findById($id);

    if ($movie === NULL)
      throw new Exception("Unable to find movie with ID {$id}");

    echo json_encode($movie);
  } elseif (isset($_GET['mode']) && $_GET['mode'] == 'archive') {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $size = isset($_GET['size']) ? (int)$_GET['size'] : KFS\Movie::DEFAULT_PAGE_SIZE;
    $pages = KFS\Movie::getPageCount($size);
    $movies = KFS\Movie::getPaged($page, $size);
    echo json_encode(array(
      'movies' => $movies,
      'page' => $page,
      'pages' => $pages,
      'size' => $size,
      'count' => count($movies)
    ));
  } else {
    $movies = KFS\Movie::getUpcoming();
    echo json_encode($movies);
  }
} catch (PDOException $ex) {
  echo json_encode(array('error' => $ex->getMessage(), 'code' => $ex->getCode()));
} catch (Exception $ex) {
  echo json_encode(array('error' => $ex->getMessage()));
}
