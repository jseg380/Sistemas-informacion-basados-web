<?php
class DataBase {
  private mysqli $mysqli;
  private bool $connected;
  private array $config;

  /**
   * Constructor
   */
  public function __construct() {
    $this->config = parse_ini_file("config.ini");

    $this->connected = $this->connect();
  }

  /**
   * Destructor
   */
  public function __destruct() {
    $this->mysqli->close();
    $this->connected = false;
  }

  /**
   * Establish connection with the database
   * @return false An error occured when connecting to the database
   * @return true The connection was established successfully
   */
  private function connect() {
    $this->mysqli = new mysqli(...$this->config);

    if ($this->mysqli->connect_errno) {
      // throw new Exception("Error connecting the database: " . 
      //                     $this->mysqli->connect_error);
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
   * @param array $params List of paramaters values to replace in the query 
   * (The query must contain parameters placeholders with TODO)
   * @return array|'[null]'
   * An array containing the results of the query. If no matches were made then
   * an empty array will be returned. If it's not connected to the database
   * it will return an array containing the null value.
   */
  private function query(string $query, array $params = []):array {
    if (!$this->connected) {
      return [null];
    }

    $rows = [];

    if (count($params) === 0) {
      $result = $this->mysqli->query($query);
    }
    else {
      $stmt = $this->mysqli->prepare($query);
      $stmt->bind_param(str_repeat("i", count($params)), ...$params);
      $stmt->execute();
      $result = $stmt->get_result();
      $stmt->close();
    }

    if (!$result) {
      // throw new Exception("Error executing query " . $query);
      return [null];
    }

    while ($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }

    return $rows;
  }

  /**
   * Get information for creating the activities cards of the main page
   * @return array
   * Array with minimal data about the activites:
   *                test one ateows
   */
  public function getActivityCards():array {
    $query = "SELECT id, logo, logo_alt, name FROM activities;";
    $result = $this->query($query);

    return $result;
  }

  /**
   * Get information about a certain activity to generate its page
   * @param int $id Id of the activity to get information
   * @return array
   * Array with full data about the activity:
   *    name, date, price, text1, image1, text2, image2, materials, links
   */
  public function getArticle(int $id):array {
    $query = "SELECT name, date, price, text1, image1, text2, image2, materials, links FROM activities WHERE id = ?;";
    $result = $this->query($query, [$id])[0];

    return $result;
  }

  /**
   * Get comments of an article
   * @param int $id Id of the activity to get the comments from
   * @return array
   * Array with full data about the comment:
   *    id, avatar, username, date, text, email
   */
  public function getArticleComments(int $id):array {
    $query = "SELECT id, avatar, username, date, text, email FROM comments WHERE activity = ? ORDER BY date ASC;";
    $result = $this->query($query, [$id]);

    return $result;
  }

  public function getForbiddenWords():array {
    $query = "SELECT word FROM forbidden_words;";
    $result = $this->query($query);
    
    // To return a simple array containing only the words
    $result = array_merge(...array_map('array_values', $result));

    return $result;
  }

  public function isValidID(int $id):bool {
    $query = "SELECT id FROM activities WHERE id = ?;";
    $result = $this->query($query, [$id]);

    return (count($result) > 0);
  }
}
?>
