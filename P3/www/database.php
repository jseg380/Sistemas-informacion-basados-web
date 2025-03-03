<?php
class DataBase {
  private mysqli $mysqli;
  private bool $connected;
  private array $config;

  public function __construct() {
    $this->config = parse_ini_file("config.ini");

    $this->connected = $this->connect();
  }

  /**
   * Establish connection with the database
   * */
  private function connect() {
    $this->mysqli = new mysqli(...$this->config);

    if ($this->mysqli->connect_errno) {
      // throw new Exception("Error connecting the database: " . $this->mysqli->connect_error);
      echo("Error connecting the database: " . $this->mysqli->connect_error);
      return false;
    }

    return true;
  }

  /**
   * General query
   * 
   * This function takes a string as a valid query and executes it
   *
   * @param string $query Query to execute
   * @return null An error occured in the DB connection
   * @return [] The query executed successfully but it was empty
   * @return array The query executed successfully but it was empty
   * */
  private function query(string $query, array $params = []):array {
    if (!$this->connected)
      return [null];

    $rows = [];
  
    $result = $this->mysqli->query($query);

    if (!$result) {
      // throw new Exception("Error executing query " . $query);
      return [null];
    }

    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc())
        $rows[] = $row;
    }

    return $rows;
  }

  public function getActivityCards():array {
    $query = "SELECT logo, logo_alt, name FROM activities;";
    $result = $this->query($query);

    return $result;
  }

  public function getArticle(string $name):array {
    $query = "SELECT name, date, price, text1, image1, text2, image2, materials, links FROM activities;";
    $result = $this->query($query)[0];

    return $result;
  }
}
?>
