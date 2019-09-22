<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
if ( !isset($_POST['serverToken']) || !isset($_POST['userToken']) || !isset($_POST['username']) ) {
    $response = ["auth" => "Error. Dev. code: 1"];
    echo json_encode($response);
    exit();
}
$serverToken = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["serverTokenTalk"];
if ($_POST['serverToken'] !== $serverToken) {
    $response = ["auth" => "Error. Dev. code: 2"];
    echo json_encode($response);
    exit();
}

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw, $serverToken);

$q = $db->prepare('SELECT name FROM user WHERE token = :token AND name = :userName');
$q->execute(array(':token' => $_POST['userToken'], ':userName' => $_POST['username']));
if ($q->rowCount() > 0) {
    $response = ["auth" => "true"] ;
    echo json_encode($response);
}
else {
    $response = ["auth" => "false"] ;
    echo json_encode($response);
}
$db = null;
exit();
?>