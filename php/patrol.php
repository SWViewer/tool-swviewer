<?php
require_once 'includes/headerOAuth.php';

if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_POST["project"]) || !isset($_POST["rev"]) || !is_numeric($_POST["rev"])) {
    echo json_encode(["result" => "Invalid request data.", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
session_write_close();

$apiUrl = "https://" . $_POST["project"] . "/w/api.php";
$rev = $_POST["rev"];

$params = ['action' => 'query', 'meta' => 'tokens', 'type' => 'csrf', 'format' => 'json'];
$token = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
$token = $token->query->tokens->csrftoken;

$params = ["action" => "review", "format" => "json", "revid" => $rev, "comment" => "via [[m:SWViewer|SWViewer]]", "token" => $token];
$res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
if (isset($res->review->result)) {
    if ($res->review->result === "Success")
        $response = ["result" => "success"];
    else
        $response = ["result" => "error", "info" => $res];
}
else
    $response = ["result" => "error", "info" => $res->error->info];
echo json_encode($response);
?>
