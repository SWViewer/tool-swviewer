<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('UPDATE user SET lastopen=0 WHERE lastopen < NOW() - INTERVAL 14 DAY');
$q->execute();
$q = $db->prepare('DELETE FROM talk WHERE msgtime < NOW() - INTERVAL 14 DAY');
$q->execute();
$db = null;
exit();
?>