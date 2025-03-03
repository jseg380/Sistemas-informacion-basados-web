<?php
include_once("php/database.php");

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
    $db = new DataBase;

    if ($db->isValidID($id)) {
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
  case "index":
  case "portada":
    header("Location:/");
    exit;
  case "":
    require "php/portada.php";
    exit;
  default:
    http_response_code(404);
    require "php/404.php";
    exit;
}


/*
include_once("database.php");

// Clean URLs
function cleanUrlPath($path) {
  $pathname = preg_split('/[\/]/', $path);
  $subdirectory = preg_replace('/\.(?:php|html)$/', "", $pathname[0]);

  // If subdirectory has any targeted extension, redirect to site without it
  if ($subdirectory !== $pathname[0]) {
    $pathname[0] = $subdirectory;
    header("Location:/" . implode("/", $pathname));
    exit;
  }

  return $pathname;
}

// Route the request based on the path
function routeRequest($pathname) {
  $parameters["subdirectory"] = $pathname[0];

  // No valid URL will have more than 2 elements in the pathname
  if (count($pathname) > 2) {
    $parameters["subdirectory"] = "error";
  }
  // Only subdirectory "actividad" accepts any parameters
  elseif (count($pathname) > 1 && $parameters["subdirectory"] === "actividad") {
    $parameters["subdirectory"] = handleActividadSubdirectory($pathname);
  }

  switch ($parameters["subdirectory"]) {
    case "index":
    case "portada":
      header("Location:/");
      break;
    case "":
      $parameters["subdirectory"] = "portada.php";
      break;
    default:
      http_response_code(404);
      require "404.php";
      exit;
  }

  return $parameters;
}

// Handle "actividad" subdirectory logic
function handleActividadSubdirectory($pathname) {
  // "actividad" subdirectory expects a positive integer, ctype_digit does not
  // accept negative nor float values so it checks correctly
  if (!ctype_digit($pathname[1])) {
    return "error";
  }

  $id = (int)$pathname[1];
  $db = new DataBase;

  if (!$db->isValidID($id)) {
    return "error";
  }

  return "actividad";
}

// Main script logic
$path = isset($_GET["path"]) ? urldecode($_GET["path"]) : "";
$pathname = cleanUrlPath($path);
$parameters = routeRequest($pathname); // [subdirectory, vars...]

$subdirectory = $parameters["subdirectory"];
$variables = array_slice($parameters, 1);

// Anonymous function to only pass the targeted variables
require function (string $subdirectory) use ( $variables) {
  require $subdirectory;
};
*/
?>
