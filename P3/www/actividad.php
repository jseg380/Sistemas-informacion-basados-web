<?php
require_once "/usr/local/lib/php/vendor/autoload.php";

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

$db = new Database;


// if (isset($_GET['ev'])) {
//   if ($_GET['ev'] == 1) {
//   } else if ($_GET['ev'] == 2) {
//   }
// }

echo $twig->render('actividad.html', []);
?>
