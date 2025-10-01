<?php
if (!isset($_GET['user']) || strlen($_GET['user']) < 1) {
    die("Name parameter missing");
}
echo "Hello, " . htmlentities($_GET['user']);
?>