<?php
header('Content-Type: text/html; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_POST["oldid"]) || !isset($_POST["server"])) {
    echo "Invalid request";
    session_write_close();
    exit();
}
session_write_close();
$url = $_POST["server"] . "/index.php?action=raw&oldid=" . $_POST["oldid"];
$content = @file_get_contents($url);
echo ($content === false) ? "Error! Loading page is not success" : $content;
?>