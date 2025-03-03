<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include_once("database.php");

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);

if (!$logged) {
  header("Location:/");
  exit;
}

if (!isset($db)) {
  $db = new Database;
}

$renderParams["role"] = $db->getRole($user["role_id"]);
$renderParams["moderator"] = in_array($user["role_id"], [3, 5]);
$renderParams["gestor"] = in_array($user["role_id"], [4, 5]);
$renderParams["super"] = ($user["role_id"] === 5);

echo $twig->render("management.html.twig", $renderParams);
?>
