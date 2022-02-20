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
$userName = $_SESSION['userName'];
session_write_close();
if (!check($_GET["action"]) || ($_GET["action"] !== "get_presets" && !check($_GET["preset_name"])) ) {
    echo json_encode(["result" => "error", "info" => "Invalid request; dev. code 2"]);
    exit();
}
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);


if ($_GET["action"] === "get_presets") {
    $q = $db->prepare('SELECT swmt, anons, registered, new, users, onlynew, regdays, oresFilter, editscount, namespaces, wlusers, wlprojects, blprojects, wikilangs, preset AS title FROM presets WHERE name = :userName GROUP BY preset;');
    $q->execute(array(':userName' => $userName));
    echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
}

if ($_GET["action"] === "create_preset" && check($_GET["preset_name"])) {
    $values = buildQuery($userName, "ins");
    $q = $db->prepare('INSERT INTO presets (swmt, anons, registered, new, users, onlynew, regdays, oresFilter, editscount, namespaces, wlusers, wlprojects, blprojects, wikilangs, preset, name) VALUES (COALESCE(:swmt, 1), COALESCE(:anons, 1), COALESCE(:registered, 1), COALESCE(:new, 1), COALESCE(:users, 0), COALESCE(:onlynew, 0), COALESCE(:regdays, 5), COALESCE(:oresFilter, 0), COALESCE(:editscount, 100), COALESCE(:namespaces, null), COALESCE(:wlusers, null), COALESCE(:wlprojects, null), COALESCE(:blprojects, null), COALESCE(:wikilangs, null), :preset, :name)');
    $q->execute($values);
    $q = $db->prepare('SELECT preset FROM presets WHERE name = :userName');
    $q->execute(array(':userName' => $userName));
    echo json_encode($q->fetchAll(PDO::FETCH_COLUMN));
}

if ($_GET["action"] === "delete_preset" && check($_GET["preset_name"])) {
    if ($_GET["preset_name"] === "Default") {
        echo json_encode(["result" => "error", "info" => "Default preset"]);
        exit();
    }
    $q = $db->prepare('SELECT preset FROM presets WHERE name = :userName');
    $q->execute(array(':userName' => $userName));
    if ($q->rowCount() > 0) {
        $q = $db->prepare('DELETE FROM presets WHERE name = :userName AND preset = :preset_name');
        $q->execute(array(':userName' => $userName, ':preset_name' => $_GET["preset_name"]));
        $q = $db->prepare('SELECT preset FROM presets WHERE name = :userName');
        $q->execute(array(':userName' => $userName));
        echo json_encode($q->fetchAll(PDO::FETCH_COLUMN));
    } else
        echo json_encode(["result" => "error", "info" => "Not found"]);
}

if ($_GET["action"] === "edit_preset" && check($_GET["preset_name"])) {
    $q = $db->prepare('SELECT preset FROM presets WHERE name = :userName');
    $q->execute(array(':userName' => $userName));
    if ($q->rowCount() > 0) {
        $values = buildQuery($userName, null);
        $q = $db->prepare('UPDATE presets SET preset = :presetnew, swmt = COALESCE(:swmt, swmt), anons = COALESCE(:anons, anons), registered = COALESCE(:registered, registered), new = COALESCE(:new, new), users = COALESCE(:users, users), onlynew = COALESCE(:onlynew, onlynew), regdays = COALESCE(:regdays, regdays), oresFilter = COALESCE(:oresFilter, oresFilter), editscount = COALESCE(:editscount, editscount), namespaces = COALESCE(:namespaces, namespaces), wlusers = COALESCE(:wlusers, wlusers), wlprojects = COALESCE(:wlprojects, wlprojects), wikilangs = COALESCE(:wikilangs, wikilangs), wikilangs = COALESCE(:wikilangs, wikilangs), blprojects = COALESCE(:blprojects, blprojects), name = COALESCE(:name, name) WHERE preset = :preset AND name = :name');
        $q->execute($values);
        $q = $db->prepare('SELECT preset FROM presets WHERE name = :userName');
        $q->execute(array(':userName' => $userName));
        echo json_encode($q->fetchAll(PDO::FETCH_COLUMN));
    } else
        echo json_encode(["result" => "error", "info" => "Not found"]);
}

if ($_GET["action"] === "get" && check($_GET["preset_name"])) {
    $q = $db->prepare('SELECT * FROM presets WHERE name = :userName AND preset = :preset_name');
    $q->execute(array(':userName' => $userName, ':preset_name' => $_GET["preset_name"]));
    if ($q->rowCount() > 0) {
        $result = $q->fetchAll();
        echo json_encode(["blprojects" => $result[0]['blprojects'], "wikilangs" => $result[0]['wikilangs'], "wikilangs" => $result[0]['wikilangs'], "swmt" => $result[0]['swmt'], "onlyanons" => $result[0]['anons'], "users" => $result[0]['users'], "wlusers" => $result[0]['wlusers'], "wlprojects" => $result[0]['wlprojects'], "namespaces" => $result[0]['namespaces'], "registered" => $result[0]['registered'], "new" => $result[0]['new'], "onlynew" => $result[0]['onlynew'], "editcount" => $result[0]['editscount'], "regdays" => $result[0]['regdays'], " oresFilter" => $result[0][' oresFilter']]);
    } else
        echo json_encode(["result" => "error", "info" => "Not found"]);
}

$db = null;

function check($check) {
    return (isset($check) && $check !== null && $check !== "") ? true : false;
}
function buildQuery($userName, $type) {
    $values = [];
    $values[":name"] = $userName;
    $values[":preset"] = $_GET["preset_name"];
    if (!isset($type) && $type !== "ins")
        $values[":presetnew"] = (check($_GET["preset_name_new"])) ? $_GET["preset_name_new"] : $_GET["preset_name"];
    $values[":swmt"] = (check($_GET["swmt"]) && in_array($_GET["swmt"], [0, 1, 2])) ? $_GET["swmt"] : null;
    $values[":anons"] = (check($_GET["anons"]) && in_array($_GET["anons"], [0, 1])) ? $_GET["anons"] : null;
    $values[":registered"] = (check($_GET["registered"]) && in_array($_GET["registered"], [0, 1])) ? $_GET["registered"] : null;
    $values[":new"] = (check($_GET["new"]) && in_array($_GET["new"], [0, 1])) ? $_GET["new"] : null;
    $values[":users"] = (check($_GET["users"]) && in_array($_GET["users"], [0, 1, 2])) ? $_GET["users"] : null;
    $values[":onlynew"] = (check($_GET["onlynew"]) && in_array($_GET["onlynew"], [0, 1])) ? $_GET["onlynew"] : null;
    $values[":regdays"] = (check($_GET["regdays"]) && is_numeric($_GET["regdays"])) ? $_GET["regdays"] : null;
    $values[":editscount"] = (check($_GET["editscount"]) && is_numeric($_GET["editscount"])) ? $_GET["editscount"] : null;
    $values[":oresFilter"] = (check($_GET["oresFilter"]) && is_numeric($_GET["oresFilter"])) ? $_GET["oresFilter"] : null;
    $values[":namespaces"] = (check($_GET["namespaces"]) && preg_match('/^[0-9,]+$/', $_GET["namespaces"])) ? $_GET["namespaces"] : "";
    $values[":wlusers"] = (check($_GET["wlusers"])) ? $_GET["wlusers"] : "";
    $values[":wlprojects"] = (check($_GET["wlprojects"]) && preg_match('/^[0-9a-zA-Z_\-,]+$/', $_GET["wlprojects"])) ? $_GET["wlprojects"] : "";
    $values[":wikilangs"] = (check($_GET["wikilangs"]) && preg_match('/^[0-9a-zA-Z_\-,]+$/', $_GET["wikilangs"])) ? $_GET["wikilangs"] : "";
    $values[":blprojects"] = (check($_GET["blprojects"]) && preg_match('/^[0-9a-zA-Z_\-,]+$/', $_GET["blprojects"])) ? $_GET["blprojects"] : "";
    $values[":wikilangs"] = (check($_GET["wikilangs"]) && preg_match('/^[0-9a-zA-Z_\-,]+$/', $_GET["wikilangs"])) ? $_GET["wikilangs"] : "";
    return $values;
}