<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include_once("database.php");

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);

$db = new Database;

$activityInfo = $db->getArticle($id);
// Unserialize arrays
$activityInfo["materials"] = unserialize($activityInfo["materials"]);
$activityInfo["links"] = unserialize($activityInfo["links"]);

$comments = $db->getArticleComments($id);

// Transform all the rows to array
$forbiddenWords = json_encode($db->getForbiddenWords());

echo $twig->render("plantilla_actividad.html.twig", [
  "activity" => $activityInfo,
  "comments" => $comments,
  "forbiddenWords" => $forbiddenWords,
]);
?>
