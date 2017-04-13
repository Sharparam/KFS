<?php
ob_start();
require_once('../include/init.php');
header('Content-Type: application/json');

try {
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $movie = KFS\Movie::findById($id);

    if ($movie === NULL) {
      http_response_code(404)
      echo json_encode(array('error' => "Unable to find movie with ID {$id}"));
      return;
    }

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
  } elseif (isset($_GET['page'])) {
    $page = $_GET['page'];
    if (KFS\Page::isPage($page)) {
      echo json_encode(KFS\Page::findByName($page));
    } else {
      http_response_code(404);
      echo json_encode(array('error' => "{$page} is not a valid page"));
    }
  } else {
    $movies = KFS\Movie::getUpcoming();
    echo json_encode($movies);
  }
} catch (PDOException $ex) {
  http_response_code(500);
  echo json_encode(array('error' => $ex->getMessage(), 'code' => $ex->getCode()));
} catch (Exception $ex) {
  http_response_code(500);
  echo json_encode(array('error' => $ex->getMessage()));
}
