<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
$usersList = [];
$usersLine = "";

getUsers("all");
getUsers("global-ipblock-exempt");
getUsers("oathauth-tester");
getCommonsUsers();
forEach ($usersList as $user) {
    $usersLine .= $user . ",";
}
$file = fopen("/data/project/swviewer/public_html/lists/globalUsers.txt", "w+");
fwrite($file, $usersLine);
fclose($file);

$file = fopen("/data/project/swviewer/public_html/lists/globalUsersLastUpdate.txt", "w+");
fwrite($file, time());
fclose($file);

function getUsers($groups)
{
    try {
        $options = array('https' => array('method' => "POST", "User-Agent: SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) PHP / getGlobals.php"));
        $context = stream_context_create($options);

        if ($groups == "all")
            $groups = "apihighlimits-requestor|captcha-exempt|wmf-researcher|wmf-ops-monitoring|sysadmin|recursive-export|vrt-permissions|new-wikis-importer|global-interface-editor|global-flow-create|global-deleter|global-bot|staff|steward|global-sysop|global-rollbacker|abusefilter-helper|founder|ombuds";
        $check = true;
        $agufrom = "";
        $cont = "";

        while ($check == true) {
            $url = "https://meta.wikimedia.org/w/api.php?action=query&format=json&list=globalallusers&formatversion=2&utf8=1&agugroup=" . $groups . "&agulimit=100" . $agufrom . $cont;
            $content = file_get_contents($url, false, $context);
            $json = json_decode($content, true);
            if (!isset($json["continue"]) || !isset($json["continue"]["agufrom"]) || !isset($json["continue"]["continue"]))
                $check = false;
            else {
                $agufrom = "&agufrom=" . urlencode($json["continue"]["agufrom"]);
                $cont = "&continue=" . $json["continue"]["continue"];
            }
            foreach ((array)$json["query"]["globalallusers"] as $user) {
                if (!in_array($user['name'], $GLOBALS['usersList']))
                    array_push($GLOBALS['usersList'], $user['name']);
            }
            sleep(1);
        }
    } catch (Exception $e) {
    }
}

function getCommonsUsers()
{
    try {
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
    } catch (Exception $e) {
    }
}

exit();
?>