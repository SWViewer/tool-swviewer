<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
session_name( 'SWViewer' );
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    $response = ["error" => "Invalid request"];
    echo json_encode($response);
    session_write_close();
    exit(0);
}

$user = $_SESSION['userName'];
$talktoken = $_SESSION['talkToken'];
$isGlobal = false;
if ($_SESSION['mode'] === "global")
    $isGlobal = true;
$isGlobalModeAccess = false;
if (isset($_SESSION['accessGlobal']))
    if ($_SESSION['accessGlobal'] === "true")
        $isGlobalModeAccess = true;
$local_wikis = "";
if (isset($_SESSION['projects']))
    $local_wikis = $_SESSION['projects'];

session_write_close();

$response = ["user" => $user, "talktoken" => $talktoken, "isGlobal" => $isGlobal, "isGlobalModeAccess" => $isGlobalModeAccess, "local_wikis" => $local_wikis];
echo json_encode($response);