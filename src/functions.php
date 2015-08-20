<?php

define('REMOTE_URI', 'https://ashley.cynic.al/check');
define('SQL_SEARCH', 'SELECT email, result FROM records WHERE email = ?');
define('SQL_INSERT', 'INSERT INTO RECORDS(email, result) VALUES (?, ?)');


/**
 * gets the database connection
 * @param  array $config the configuration options
 * @return PDO
 */
function get_database ($config = array()) {
  static $result;
  if (! $result) {
    $result = new PDO(
      sprintf('mysql:host=%s;dbname=%s', $config['host'], $config['name']),
      $config['user'],
      $config['pass']
    );
  }
  return $result;
}

/**
 * determines if a given value is a valid email address
 * @param  string  $email the raw email address
 * @return boolean
 */
function is_email ($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * gets data from the database if there is any, otherwise returns false
 * @return array|boolean
 */
function get_result_from_database ($email) {
  $pdo_connection = get_database();
  $pdo_connection = new PDO('mysql:host=127.0.0.1;dbname=am', 'am', 'am');
  $pdo_statement  = $pdo_connection->prepare(SQL_SEARCH);
  $pdo_result     = $pdo_statement->execute(array($email));
  if ($pdo_result) {
    $row = $pdo_statement->fetch(PDO::FETCH_ASSOC);
    return $row['result'];
  }

  return false;

}

/**
 * gets data from a remote uri
 * @return string json data
 */
function get_data_from_uri ($email) {
  $data   = array('email' => $email);
  $query  = http_build_query($data);
  $handle = curl_init();

  curl_setopt($handle, CURLOPT_POST, count($data));
  curl_setopt($handle, CURLOPT_POSTFIELDS, $query);
  curl_setopt($handle, CURLOPT_URL, REMOTE_URI);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_HEADER, false);
  curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
  return json_decode(curl_exec($handle), true);
}

/**
 * takes data, and stores it in the database
 * @param [type] $data [description]
 */
function set_result_to_database ($email, $data) {
  $pdo_connection = get_database();
  $result         = (int)$data['found'];
  $pdo_statement  = $pdo_connection->prepare(SQL_INSERT);
  $pdo_result     = $pdo_statement->execute(array($email, $result));
}

/**
 * gets an email address from a given csv line
 * @param  [type] $line [description]
 * @return [type]       [description]
 */
function get_email_from_line ( $line ) {
  foreach ($line as $column) {
    if (is_email($column)) {
      return strtolower($column);
    }
  }
  return false;
}

/**
 * handles data, by checking in the db, if not there, a request to the uri
 * @param  string $email the email
 * @param  array $data  the array of data
 * @return array        the merged data
 */
function handle_email ($email) {
  $result = get_result_from_database($email);
  if (! $result) {
    $record = get_data_from_uri($email);
    set_result_to_database($email, $record);
    $result = get_result_from_database($email);
  }

  return $result;
}