<?php
require_once "/usr/local/lib/php/vendor/autoload.php";
include_once("database.php");

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);


if (!isset($db)) {
  $db = new Database;
}

$activityInfo = $db->getArticle($id);
// Unserialize arrays
$activityInfo["materials"] = unserialize($activityInfo["materials"]);
$activityInfo["links"] = unserialize($activityInfo["links"]);

$comments = $db->getArticleComments($id);

// Transform all the rows to array
$forbiddenWords = json_encode($db->getForbiddenWords());

// Render parameters
$extraRenderParams= [
  "activity" => $activityInfo,
  "comments" => $comments,
  "forbiddenWords" => $forbiddenWords,
  "allowedComment" => ($logged && in_array($user["role_id"], [2, 5])),
];

echo $twig->render("plantilla_actividad.html.twig", $renderParams + $extraRenderParams);

?>
