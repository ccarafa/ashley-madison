<?php
// require needed functions and config
require './functions.php';
require './config.php';

$database_connection = get_database($config);
$lines = file('./contacts.csv', FILE_SKIP_EMPTY_LINES);
$length = count($lines);

foreach ($lines as $line) {
  $line  = explode(',', $line);
  $email = get_email_from_line($line);
  if ($email) {
    handle_email($email);
  }
}