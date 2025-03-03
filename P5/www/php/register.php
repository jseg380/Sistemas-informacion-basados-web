<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include_once("database.php");

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);

$errors = [];


// If the method is POST try registering with the provided credentials
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];
  $email = $_POST["email"];

  // Username, password and email must be not empty
  if ($username === "" || $password === "" || $email === "") {
    $errors[] = "No se permiten campos vacíos.";
  }

  if (!isset($db)) {
    $db = new Database;
  }

  $result = $db->createUser($username, $password, $email);

  // Registration successful
  if ($result === 0) {
    if (!$logged) {
      $_SESSION["userID"] = $db->getUser($username, $password)["id"];
    }
    header("Location:/");
  }

  // User already exists
  if ($result === 1) {
    $errors[] = "Ese usuario ya existe";
  }
}

$renderParams["errors"] = $errors;

echo $twig->render("register.html.twig", $renderParams);
?>
