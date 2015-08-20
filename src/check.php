<?php
// require needed functions and config
require './functions.php';
require './config.php';

$response = array();
try {
  $database_connection = get_database($config);
  $email = trim(strtolower($_GET['email']));
  if (! is_email($email)) {
    $response['errors'] = 'invalid email address : ' . $email;
  }
  $response['result'] = handle_email($email);

} catch (PDOException $exception) {
  $response['errors'] = $exception->getMessage();
}

header('Content-type: application/json');
echo json_encode($response);
