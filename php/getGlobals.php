<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: text/html; charset=utf-8');
session_name('SWViewer');
session_start();
if ((!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) && (!isset($_GET['token_proxy']))) {
    echo "Invalid request";
    session_write_close();
    exit(0);
}
session_write_close();
if (isset($_GET['token_proxy'])) {
    $serverToken = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["serverTokenTalk"];
    if ($serverToken !== $_GET["token_proxy"]) {
        echo "Invalid request";
        exit(0);
    }
}

$usersList = [];

getUsers("all");
getUsers("global-ipblock-exempt");
getUsers("oathauth-tester");
getCommonsUsers();

$output = "";
forEach ($usersList as $user) {
    $output .= $user . ",";
}
echo $output;

function getUsers($groups)
{
    $options = array('https' => array('method' => "POST", "User-Agent: SWViewer/1.3 (https://swviewer.toolforge.org; swviewer@tools.wmflabs.org) PHP / getGlobals.php"));
    $context = stream_context_create($options);

    if ($groups == "all")
        $groups = "apihighlimits-requestor|captcha-exempt|wmf-researcher|wmf-ops-monitoring|sysadmin|recursive-export|vrt-permissions|new-wikis-importer|global-interface-editor|global-flow-create|global-deleter|global-bot|staff|steward|global-sysop|global-rollbacker|abusefilter-helper|founder|ombuds";
    $check = true;
    $agufrom = "";
    $cont = "";

    while ($check === true) {
        $url = "https://meta.wikimedia.org/w/api.php?action=query&format=json&list=globalallusers&formatversion=2&utf8=1&agugroup=" . $groups . "&agulimit=100" . $agufrom . $cont;
        $content = file_get_contents($url, false, $context);
        $json = json_decode($content, true);
        if (!isset($json["continue"]) || !isset($json["continue"]["agufrom"]) || !isset($json["continue"]["continue"]))
            $check = false;
        else {
            $agufrom = "&agufrom=" . urlencode($json["continue"]["agufrom"]);
            $cont = "&continue=" . $json["continue"]["continue"];
        }
        foreach ($json["query"]["globalallusers"] as $user) {
            if (!in_array($user['name'], $GLOBALS['usersList']))
                array_push($GLOBALS['usersList'], $user['name']);
        }
    }
}

function getCommonsUsers()
{
    $options = array('https' => array('method' => "POST", "User-Agent: SWViewer/1.3 (https://swviewer.toolforge.org; swviewer@tools.wmflabs.org) PHP / getGlobals.php"));
    $context = stream_context_create($options);

    $groups = "sysop|filemover";
    $check = true;
    $aufrom = "";
    $cont = "";

    while ($check === true) {
        $url = "https://commons.wikimedia.org/w/api.php?action=query&format=json&list=allusers&formatversion=2&utf8=1&augroup=" . $groups . "&aulimit=100" . $aufrom . $cont;
        $content = file_get_contents($url, false, $context);
        $json = json_decode($content, true);
        if (!isset($json["continue"]) || !isset($json["continue"]["aufrom"]) || !isset($json["continue"]["continue"]))
            $check = false;
        else {
            $aufrom = "&aufrom=" . urlencode($json["continue"]["aufrom"]);
            $cont = "&continue=" . $json["continue"]["continue"];
        }
        foreach ($json["query"]["allusers"] as $user) {
            if (!in_array($user['name'], $GLOBALS['usersList']))
                array_push($GLOBALS['usersList'], $user['name']);
        }
    }
}



?>