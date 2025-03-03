<?php
include_once("php/database.php");


// Database connection, only one per request
$db = new DataBase;


// Session management
session_start();

$logged = isset($_SESSION["userID"]);

// By default no one has permissions
$permission = false;

$user = [];
// If logged in, check if permissions for management panel are enabled
if ($logged) {
  global $user;
  $user = $db->getUserByID($_SESSION["userID"]);
  if ($user["role_id"] > 2) {
    $permission = true;
  }
}

// Parameters for rendering the template with Twig
$renderParams = ["logged" => $logged, "management" => $permission];


// Clean URLs

// Get the request URL path
$path = isset($_GET["path"]) ? urldecode($_GET["path"]) : "";

$pathname = preg_split('/[\/]/', $path);
$subdirectory = $pathname[0];
$subdirectory = preg_replace('/\.(?:php|html)$/', "", $pathname[0]);


// If subdirectory has any targeted extension, redirect to site without it
if ($subdirectory !== $pathname[0]) {
  $pathname[0] = $subdirectory;
  header("Location:/" . implode("/", $pathname));
  exit;
}


// No valid URL will have more than 2 elements in the pathname
if (count($pathname) > 2) {
  $subdirectory = "error";
}
// Only subdirectory "actividad" accepts any parameters
elseif (count($pathname) > 1 && $subdirectory === "actividad") {
  // ctype_digit does not accept negative or float values
  if ($subdirectory === "actividad" && ctype_digit($pathname[1])) {
    // Setting $id to later use it in actividad.php
    $id = (int)$pathname[1];

    if ($db->isValidActivityID($id)) {
      // Anonymous function to only pass $id variable
      require "php/actividad.php";
      exit;
    }
    else {
      $subdirectory = "error";
    }
  }
  else {
    $subdirectory = "error";
  }
}

// Route the request based on the path
switch ($subdirectory) {
  case "login":
    if (!$logged) {
      require "php/login.php";
      exit;
    }
  case "register":
    if (!$logged) {
      require "php/register.php";
      exit;
    }
  case "logout":
    if ($logged) {
      require "php/logout.php";
      exit;
    }
  case "index":
  case "portada":
    header("Location:/");
    exit;
  case "":
    require "php/portada.php";
    exit;
  case "management":
    if ($logged && $permission) {
      require "php/management.php";
      exit;
    }
  default:
    http_response_code(404);
    require "php/404.php";
    exit;
}
?>
