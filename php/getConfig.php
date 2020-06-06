<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: text/html; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    echo "Invalid request";
    session_write_close();
    exit();
}
session_write_close();

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('SELECT id FROM verify');
$q->execute();
$result = $q->fetch();
$rev = $result["id"];
$db = null;

$url = "https://meta.wikimedia.org/w/index.php?title=SWViewer/config.json&oldid=" . $rev . "&action=raw";
echo file_get_contents($url);
?>