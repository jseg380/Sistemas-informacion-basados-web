<?php
require_once "/usr/local/lib/php/vendor/autoload.php";

$loader = new \Twig\Loader\FilesystemLoader("templates");
$twig = new \Twig\Environment($loader);

echo $twig->render("404.html.twig");
?>
