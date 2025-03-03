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
      $str_params = "";
      foreach ($params as $var) {
        switch (gettype($var)) {
          case "integer":
            $str_params .= "i";
            break;
          case "double":
            $str_params .= "d";
            break;
          case "string":
            $str_params .= "s";
            break;
        }
      }

      $stmt->bind_param($str_params, ...$params);
      try {
        $stmt->execute();
        $result = $stmt->get_result();
      } catch (Exception $e) {
        return [null];
      }
      finally {
        $stmt->close();
      }
    }

    if (!$result) {
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
   * @return array Array with full data about the comment:
   *    id, avatar, username, date, text, email
   */
  public function getArticleComments(int $id):array {
    $query = "SELECT id, avatar, username, date, text, email FROM comments WHERE activity = ? ORDER BY date ASC;";
    $result = $this->query($query, [$id]);

    return $result;
  }

  /**
   * Get array containing the forbidden words which comments cannot contain
   * @return array Array with all the forbidden words
   * */
  public function getForbiddenWords():array {
    $query = "SELECT word FROM forbidden_words;";
    $result = $this->query($query);
    
    // To return a simple array containing only the words
    $result = array_merge(...array_map('array_values', $result));

    return $result;
  }

  /**
   * Check if there is any activity with that id in the database
   * @param int $id Id of the activity
   * @return bool True if the ID corresponds to an existing activity
   * */
  public function isValidActivityID(int $id):bool {
    $query = "SELECT id FROM activities WHERE id = ?;";
    $result = $this->query($query, [$id]);

    return (count($result) > 0);
  }

  /**
   * Check if there is any user with those credentials registered
   * @param string $username Username of the user
   * @param string $password Hashed password of the user
   * @param string $email Email of the user (null to skip this check)
   * @return bool True if there is such user in the database
   * */
  public function existsUser(string $username, string $password, string $email=null):bool {
    $query = "SELECT * FROM users WHERE username = ?";
    $params = [$username];
    if ($email !== null) {
      $query .= " and email = ?";
      $params[] = $email;
    }
    $query .= ";";

    $result = $this->query($query, $params);

    if (count($result) > 0 && password_verify($password, $result[0]["password"])) {
      return true;
    }

    return false;
  }

  /**
   * Create a new user and insert it into the database
   * @param string $username Username of the user
   * @param string $password Password of the user (will be hashed for storing)
   * @param string $email Email of the user
   * @return int Code indicating the exit status:
   *    0: User creation successful
   *    1: User already exists
   * */
  public function createUser(string $username, string $password, string $email):int {
    // Unsuccessful creation if the user was already created
    if ($this->existsUser($username, $password, $email)) {
      return 1;
    }

    $hashPasswd = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $this->query($query, [$username, $hashPasswd, $email]);

    return 0;
  }

  public function getUser(string $username, string $password):array {
    $query = "SELECT * FROM users WHERE username = ?;";
    $result = $this->query($query, [$username]);
    $user = [];

    foreach ($result as $row) {
      if (password_verify($password, $row["password"])) {
        $user = $row;
      }
    }

    return $user;
  }

  public function getUserByID(int $id):array {
    $query = "SELECT * FROM users WHERE id = ?;";
    $result = $this->query($query, [$id]);
    $user = $result[0];

    return $user;
  }

  public function updateUserInfo():bool {
    return false;
  }

  public function getRole(int $id) {
    $query = "SELECT role_name FROM roles WHERE id = ?;";
    $result = $this->query($query, [$id]);
    $roleName = $result[0]["role_name"];

    return $roleName;
  }
}
?>
