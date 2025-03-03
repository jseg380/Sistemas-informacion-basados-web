<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include_once("database.php");

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);

$errors = [];


// If the method is POST try login with the provided credentials
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  // Username and password must be not empty
  if ($username === "" || $password === "") {
    $errors[] = "No se permiten campos vacíos.";
  }

  if (!isset($db)) {
    $db = new Database;
  }

  // Check if there is a user with those credentials
  if ($db->existsUser($username, $password)) {
    if (!$logged) {
      $_SESSION["userID"] = $db->getUser($username, $password)["id"];
    }
    header("Location:/");
  }
  else {
    $errors[] = "No existe tal usuario";
  }
}

$renderParams["errors"] = $errors;

echo $twig->render("login.html.twig", $renderParams);
?>
