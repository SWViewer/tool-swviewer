<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['token_proxy']) || !isset($_GET['username']) || !isset($_GET['preset_name'])) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
if (isset($_GET['token_proxy'])) {
    $serverToken = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["serverTokenTalk"];
    if ($serverToken !== $_GET["token_proxy"]) {
        echo json_encode(["result" => "error", "info" => "Invalid request", "code" => 2]);
        exit();
    }
}

$username = $_GET['username'];

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

if (check($_GET["preset_name"])) {
    $q = $db->prepare('SELECT * FROM presets WHERE name = :userName AND preset = :preset_name');
    $q->execute(array(':userName' => $username, ':preset_name' => $_GET["preset_name"]));
    if ($q->rowCount() > 0) {
        $result = $q->fetchAll();
        $raw_result = ["blprojects" => $result[0]['blprojects'], "wikilangs" => $result[0]['wikilangs'], "swmt" => $result[0]['swmt'], "onlyanons" => $result[0]['anons'], "users" => $result[0]['users'], "wlusers" => $result[0]['wlusers'], "wlprojects" => $result[0]['wlprojects'], "namespaces" => $result[0]['namespaces'], "registered" => $result[0]['registered'], "new" => $result[0]['new'], "onlynew" => $result[0]['onlynew'], "editcount" => $result[0]['editscount'], "regdays" => $result[0]['regdays'], "oresFilter" => $result[0]['oresFilter']];
        $q = $db->prepare('SELECT isGlobal, isGlobalAccess, local_wikis FROM user WHERE name = :userName');
        $q->execute(array(':userName' => $username));
        $result2 = $q->fetchAll();
        $raw_result["isGlobal"] = $result2[0]['isGlobal'];
        $raw_result["isGlobalModeAccess"] = $result2[0]['isGlobalAccess'];
        $raw_result["local_wikis"] = $result2[0]['local_wikis'];
        echo json_encode($raw_result);
    } else
        echo json_encode(["result" => "error", "info" => "Not found"]);
}

$db = null;

function check($check) {
    return (isset($check) && $check !== null && $check !== "") ? true : false;
}