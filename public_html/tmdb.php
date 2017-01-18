<?php
require_once('../include/init.php');

header('Content-Type: application/json');

$key = KFS\Config::TMDB_KEY;

$query = $_GET['q'];
$type = $_GET['t'];

$endpoint = 'https://api.themoviedb.org/3';

$data = array('api_key' => $key);

switch ($type) {
  case 'search':
    $data['query'] = $query;
    $data['include_adult'] = true;
    $path = '/search/movie';
    break;
  case 'get':
    $path = "/movie/{$query}";
    $data['append_to_response'] = 'credits';
    break;
  default:
    echo json_encode(array('error' => 'Unsupported action.'));
    exit();
    break;
}

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $endpoint . $path . '?' . http_build_query($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

$result = curl_exec($curl);

curl_close($curl);

echo $result;
