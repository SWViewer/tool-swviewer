<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    echo json_encode(["error" => "Invalid request"]);
    session_write_close();
    exit();
}

$user = $_SESSION['userName'];
$talktoken = $_SESSION['talkToken'];
$isGlobal = ($_SESSION['mode'] === "global") ? true : false;
$isGlobalModeAccess = false;
if (isset($_SESSION['accessGlobal']))
    if ($_SESSION['accessGlobal'] === "true")
        $isGlobalModeAccess = true;
$local_wikis = "";
if (isset($_SESSION['projects']))
    if ($_SESSION['projects'] !== null)
        $local_wikis = $_SESSION['projects'];
$userRole = "none";
if (isset($_SESSION['userRole']))
    if ($_SESSION['userRole'] !== null)
        $userRole = $_SESSION['userRole'];

session_write_close();
echo json_encode(["user" => $user, "talktoken" => $talktoken, "isGlobal" => $isGlobal, "userRole" => $userRole, "isGlobalModeAccess" => $isGlobalModeAccess, "local_wikis" => $local_wikis]);
?>