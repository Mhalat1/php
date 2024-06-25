<?php 

session_unset();
setcookie(session_name(), '', time() - 3600, '/');
//supprime le cooki coté client

session_destroy();
header('Location: index.php');

?>