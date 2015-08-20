<?php
// require needed functions and config
require './functions.php';
require './config.php';

$name  = $_FILES["contacts"]["name"];
$type  = $_FILES["contacts"]["type"];
$size  = $_FILES["contacts"]["size"];
$temp  = $_FILES["contacts"]["temp_name"];
$error = $_FILES["contacts"]["error"];
$filename = './tmp/' . date("U") . '.csv';

$response = array(
  'raw' => $_FILES,
  'results' => array()
);

if ($error > 0) {
    $response['error'] = "Error uploading file! code $error.";
} else {

  move_uploaded_file($_FILES["contacts"]["tmp_name"], $filename);
  $database_connection = get_database($config);
  $lines = file($filename, FILE_SKIP_EMPTY_LINES);
  $length = count($lines);

  foreach ($lines as $line) {
    $line  = explode(',', $line);
    $email = get_email_from_line($line);
    if ($email) {
      $result = handle_email($email);
      $response['results'][] = array($email, $result);
    }
  }
}

header('Content-type: application/json');
echo json_encode($response);
