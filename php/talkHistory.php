<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_POST['action']) || ((!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) && !isset($_POST['serverToken']))) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}

$serverToken = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["serverTokenTalk"];
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8mb4", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

if (isset($_POST['serverToken']))
    if ($_POST['serverToken'] == $serverToken && $_POST['action'] == "save")
        if (isset($_POST['text']) && isset($_POST['username'])) {
            $q = $db->prepare('INSERT INTO talk (name, text) VALUES (:name, :text)');
            $q->execute(array(':name' => $_POST['username'], ':text' => $_POST['text']));
        }
unset($serverToken);

if ($_POST['action'] == "get" && isset($_SESSION['tokenKey']) && isset($_SESSION['tokenSecret']) && isset($_SESSION['userName'])) {
    $q = $db->prepare('SELECT * FROM talk WHERE msgtime > NOW() - INTERVAL 5 DAY ORDER BY msgtime ASC');
    $q->execute();
    $res[] = null;
    $rows = $q->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        if (date('Ymd') == date('Ymd', strtotime($row['msgtime'])))
            $res[0][] = $row;
        else if (date('Ymd', strtotime('-1 day', strtotime('now'))) == date('Ymd', strtotime($row['msgtime'])))
            $res[1][] = $row;
        else if (date('Ymd', strtotime('-2 day', strtotime('now'))) == date('Ymd', strtotime($row['msgtime'])))
            $res[2][] = $row;
        else if (date('Ymd', strtotime('-3 day', strtotime('now'))) == date('Ymd', strtotime($row['msgtime'])))
            $res[3][] = $row;
        else if (date('Ymd', strtotime('-4 day', strtotime('now'))) == date('Ymd', strtotime($row['msgtime'])))
            $res[4][] = $row;
    }
    echo json_encode($res);
}

$db = null;
session_write_close();
?>