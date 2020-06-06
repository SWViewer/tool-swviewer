<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
session_write_close();

$serverToken = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["serverTokenTalk"];
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('SELECT name FROM user WHERE lastopen >= NOW() - INTERVAL 4 DAY ORDER BY lastopen ASC');
$q->execute();
$rows = $q->fetchAll(PDO::FETCH_ASSOC);

$result[] = null;
$count = count($rows) - 1;
if ($count > -1) {
    $indexArray = 0;
    while ($indexArray <= $count) {
        $result[$indexArray] = $rows[$indexArray]["name"];
        $indexArray++;
    }
}
echo json_encode($result);

$db = null;
?>