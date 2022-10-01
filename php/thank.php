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
$params = ['action' => 'thank', 'rev' => $rev, 'source' => 'swviewer', 'token' => $token, 'utf8' => '1', 'format' => 'json'];
$res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
if (isset($res->result->success))
    $responce = ["result" => "success", "user" => $res->result->recipient];
else
    $responce = ["result" => "error", "info" => $res->error->info];
echo json_encode($responce);
?>

