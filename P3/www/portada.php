<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include("database.php");

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$db = new Database;

$activitiesCards = $db->getActivityCards();

echo $twig->render('portada.html', ['activitiesCards' => $activitiesCards]);
?>
