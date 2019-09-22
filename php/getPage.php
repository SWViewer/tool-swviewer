<?php
header('Content-Type: text/html; charset=utf-8');
session_name( 'SWViewer' );
session_start();
if ((isset($_SESSION['tokenKey']) == false) or (isset($_SESSION['tokenSecret']) == false) or (isset($_SESSION['userName']) == false) or (isset($_POST["oldid"]) == false) or (isset($_POST["server"]) == false)) {
    echo "Invalid request";
    session_write_close();
    exit(0);
}
session_write_close();
$url = $_POST["server"] . "/index.php?action=raw&oldid=" . $_POST["oldid"];
$content = @file_get_contents($url);

if ($content === FALSE)
    echo "Error! Loading page is not success";
else
    echo $content;
?>