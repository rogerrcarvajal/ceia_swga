<?php
session_start();
session_unset();
session_destroy();
header("Location: /ceia_swga/public/index.php");
exit();
?>