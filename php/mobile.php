<?php
if (!isset($_POST['action']))
    exit();

if ($_POST["action"] == "toLegacy" || $_POST["action"] == "toStable") {
    $ver = "Stable";
    if ($_POST["action"] == "toLegacy")
        $ver = "Legacy";
    if ($_POST["action"] == "toStable")
        $ver = "Stable";
    setcookie("SWViewerVersion", $ver, time()+31536000, "/", null, 1);
}
if ($_POST["action"] == "toLeft" || $_POST["action"] == "toRight") {
    $orient = "left";
    if ($_POST["action"] == "toLeft")
        $orient = "left";
    if ($_POST["action"] == "toRight")
        $orient = "right";
    setcookie("SWViewerOrientation", $orient, time()+31536000, "/", null, 1);
}

if ($_POST["action"] == "toBottom" || $_POST["action"] == "toTop") {
    $direct = "top";
    if ($_POST["action"] == "toBottom")
        $direct = "bottom";
    if ($_POST["action"] == "toTop")
        $direct = "top";
    setcookie("SWViewerDirect", $direct, time()+31536000, "/", null, 1);
}

if ($_POST["action"] == "toMobile" || $_POST["action"] == "toNormal") {
    $mobile = "normal";
    if ($_POST["action"] == "toMobile")
        $mobile = "mobile";
    if ($_POST["action"] == "toNormal")
        $mobile = "normal";
    setcookie("SWViewerMobile", $mobile, time()+31536000, "/", null, 1);
}
if ($_POST["action"] == "toOff" || $_POST["action"] == "toOn") {
    $sounf = "on";
    if ($_POST["action"] == "toOff")
        $sound = "off";
    if ($_POST["action"] == "toOn")
        $sound = "on";
    setcookie("SWViewerSound", $sound, time()+31536000, "/", null, 1);
}
?>