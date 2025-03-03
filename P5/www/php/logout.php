<?php
if ($logged) {
  session_destroy();
}
header("Location:/");
exit;
?>
