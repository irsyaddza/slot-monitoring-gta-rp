<?php
session_start();
session_destroy();
header("Location: /slot/index.php");
exit();
?>