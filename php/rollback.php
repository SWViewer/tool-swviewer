<?php
require_once 'includes/headerOAuth.php';

if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_POST["page"]) || !isset($_POST["wiki"]) || !isset($_POST["project"]) || !isset($_POST["user"]) || !isset($_POST["rbmode"]) || ($_POST["rbmode"] === "undo" && (!isset($_POST["basetimestamp"]) || !isset($_POST["id"])))) {
    echo json_encode(["result" => "Invalid request data.", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
$apiUrl = $_POST["project"];
$basetimestamp = "";
$id = "";
if ($_POST["rbmode"] === "undo") {
    $basetimestamp = $_POST["basetimestamp"];
    $id = $_POST["id"];
}
$userName = $_SESSION['userName'];
$page = $_POST["page"];
$user = $_POST["user"];
$wiki = $_POST["wiki"];
$mode = $_POST["rbmode"];
session_write_close();
$summary = $res2 = $rev = null;

// Get token
$params = ['action' => 'query', 'meta' => 'tokens', 'format' => 'json'];
$params["type"] = ($mode === "undo") ? "csrf" : "rollback";
$tokentype = $params["type"]."token";

$token_r = $client->makeOAuthCall($accessToken, $apiUrl, true, $params);
$token = json_decode($token_r);
if (!isset($token->query))
    file_put_contents("error.txt", $userName . json_encode($token_r), FILE_APPEND);
$token = $token->query->tokens->$tokentype;

// Now perform rollback or undo
if ($mode === "rollback") {
    $params = ['action' => 'rollback', 'title' => $page, 'user' => $user, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
    if (isset($_POST["summary"]))
        $params["summary"] = $_POST["summary"];
    $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
} else {
    $params = ['action' => 'query', 'prop' => 'revisions', 'rvstartid' => $id, 'titles' => $page, 'rvprop' => 'ids|user', 'rvlimit' => 1, 'rvexcludeuser' => $user, 'utf8' => '1', 'format' => 'json'];
    $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
    forEach ($res->query->pages as $key => $p) {
        if ($key !== "-1")
            $res2 = $p;
    }
    if ($res2 !== null) {
        if (!isset($res2->revisions)) {
            $response = ["result" => "All edits on this page by same user.", "code" => "new page"];
            echo json_encode($response);
            exit();
        }
        if ($res2->revisions[0]->revid !== "0")
            $rev = $res2->revisions[0]->revid;
    }
    if ($rev !== null) {
        $summary = str_replace("$1", $res2->revisions[0]->user, "Restore to the last revision by [[User:$1|$1]]");
        if (isset($_POST["summary"]))
            if ($_POST["summary"] !== "")
                $summary = str_replace("$1", $res2->revisions[0]->user, $_POST["summary"]);
        $params = ['action' => 'edit', 'title' => $page, 'undo' => $id, 'undoafter' => $rev, 'nocreate' => '1', 'watchlist' => 'nochange', 'minor' => 1, 'summary' => $summary, 'basetimestamp' => $basetimestamp, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
        $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
    }
}

// Catching bad responses
$typeaction = ($mode === "undo") ? "edit" : "rollback";
if (!isset($res->$typeaction->title) || isset($res->$typeaction->nochange)) {
    $res = json_decode(json_encode($res), True);
    if (isset($res[$typeaction]["nochange"]))
        $response = ["code" => "alreadyrolled", "result" => "Edits is already undid."];
    else
        $response = (!isset($res["error"])) ? ["result" => "Unknow error", "code" => "Unknow error: RB2"] : ["result" => $res["error"]["info"], "code" => $res["error"]["code"]];
    echo json_encode($response);
    exit();
}

// Send result to DB and return result
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
if ($mode === "rollback") {
    $q->execute(array(':user' => $userName, ':type' => 'rollback', ':wiki' => $wiki, ':title' => strval($res->rollback->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->rollback->old_revid) . '&diff=' . strval($res->rollback->revid) . '/'));
    $res = json_decode(json_encode($res), True);
    $q = $db->prepare('UPDATE stats SET rollback=rollback + 1 WHERE user=:username');
    $q->execute(array(':username' => $userName));
    echo json_encode(["result" => "Success", "summary" => $res["rollback"]["summary"], "oldrevid" => $res["rollback"]["old_revid"], "newrevid" => $res["rollback"]["revid"], "user" => $userName, "type" => "rolback"]);
} else {
    $q->execute(array(':user' => $userName, ':type' => 'undo', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
    $res = json_decode(json_encode($res), True);
    $q = $db->prepare('UPDATE stats SET undos=undos + 1 WHERE user=:username');
    $q->execute(array(':username' => $userName));
    echo json_encode(["result" => "Success", "summary" => $summary, "oldrevid" => $res["edit"]["oldrevid"], "newrevid" => $res["edit"]["newrevid"], "user" => $userName, "type" => "undo"]);
}
$db = null;
?>