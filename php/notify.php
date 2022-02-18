<?php
require_once 'includes/headerOAuth.php';
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_POST["ts"])) {
    echo "Invalid request";
    session_write_close();
    exit();
}
session_write_close();

$timestamp = $_POST["ts"];
$apiUrl = "https://meta.wikimedia.org/w/api.php";
$params = ['action' => 'query', 'format' => 'json', 'uselang' => 'ru', 'meta' => 'notifications', 'notcrosswikisummary' => 1,  'notfilter' => "!read", 'utf8' => '1', 'notlimit' => '50'];
$wikis = [];
$response = [];

$result = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));

foreach($result->query->notifications->list as $list) {
    if ($list->timestamp->utcunix > $timestamp) {
        if ($list->type !== "foreign") {
            $r = getNotify($list, $apiUrl, "metawiki");
            if ($r) array_push($response, $r);
        }
        else {
            $key = key($list->sources);
            if (!in_array($key, $wikis)) {
                array_push($wikis, $key);
                $apiUrl = $list->sources->$key->url;
                $result2 = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
                foreach($result2->query->notifications->list as $list2) {
                    if ($list2->timestamp->utcunix > $timestamp)
                        if ($list2->type !== "foreign") {
                            $r = getNotify($list2, $apiUrl, $key);
                            if ($r) array_push($response, $r);
                        }
                }
            }
        }
    }
}

if (sizeof($response) > 0)
    echo json_encode($response);

function getNotify($content, $api, $wiki) {
    if ($content->type === "edit-user-talk")
        return ["type" => "TP", "wiki" => $wiki, "agent" => $content->agent->name, "url" => str_replace("/w/api.php", "/wiki/", $api) . urlencode(str_replace(" ", "_", $content->title->full))];
    if ($content->type === "mention")
        return ["type" => "mention", "wiki" => $wiki, "agent" => $content->agent->name, "url" => str_replace("/w/api.php", "/wiki/", $api) . urlencode(str_replace(" ", "_", $content->title->full))];
    if ($content->type === "reverted")
        return ["type" => "revert", "wiki" => $wiki, "agent" => $content->agent->name, "url" => $api . "index.php?title=" . urlencode(str_replace(" ", "_", $content->title->full))];
    if ($content->type === "edit-thank")
        return ["type" => "thank", "wiki" => $wiki, "agent" => $content->agent->name, "url" => $api . "index.php?title=" . urlencode(str_replace(" ", "_", $content->title->full))];
    return null;
}