<?php
require_once 'includes/headerOAuth.php';
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_POST["title"]) || !isset($_POST["wiki"]) ) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
$userName = $_SESSION['userName'];
session_write_close();

$wiki = $_POST["wiki"];
$project = $_POST["project"];
$title = $_POST["title"];
$reason = "";
if (isset($_POST["reason"]) && $_POST["reason"] !== "none")
    $reason = ": " . $_POST["reason"];
$text = "\n" . "* Please delete " . "{{plain link|" . $project . "/wiki/" . str_replace(" ", "_", $title) . "|" . $wiki . ":" . str_replace("_", " ", $title) . "}}" . $reason . ". ~~~~";
$page = "Global sysops/Requests";
$summary = "Requesting deletion";
$apiUrl = "https://meta.wikimedia.org/w/api.php";
$findText = $project . "/wiki/" . str_replace(" ", "_", $title);

$res_content = @file_get_contents(str_replace("/api.php", "/index.php", $apiUrl) . "?action=raw&title=" . urlencode($page));
if (substr_count($res_content, $findText)) {
    echo json_encode(["result" => "error", "info" => "Already reported"]);
    exit();
}


$params = ['action' => 'query', 'meta' => 'tokens', 'type' => 'csrf', 'format' => 'json'];
$token = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params))->query->tokens->csrftoken;

$params = ['action' => 'edit', 'title' => $page, 'appendtext' => $text, 'recreate' => '0', 'nocreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
$res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));

if (isset($res->edit->title)) {
    if (isset($res->edit->nochange)) {
        echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
        exit();
    }
            }
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);
$q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
$q->execute(array(':user' => $userName, ':type' => 'report', ':wiki' => 'metawiki', ':title' => 'GSR', ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));

$q = $db->prepare('UPDATE stats SET report=report + 1 WHERE user=:username');
$q->execute(array(':username' => $userName));
$db = null;

$response = ["result" => "sucess"];
echo json_encode($response);
?>