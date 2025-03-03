<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include_once("database.php");

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);

if (!isset($db)) {
  $db = new Database;
}

$activitiesCards = $db->getActivityCards();
$renderParams["activitiesCards"] = $activitiesCards;

echo $twig->render("portada.html.twig", $renderParams);
?>
