<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: text/html; charset=utf-8');
session_name( 'SWViewer' );
session_start();
if ((isset($_SESSION['tokenKey']) == false) or (isset($_SESSION['tokenSecret']) == false) or (isset($_SESSION['userName']) == false)) {
    echo "Invalid request";
    session_write_close();
    exit(0);
}
session_write_close();

$usersList = [];

getUsers("all");
getUsers("global-ipblock-exempt");
getUsers("oathauth-tester");

forEach($usersList as $user) {
    echo $user . ",";
}




function getUsers($groups) {
    $options = array('https'=>array('method'=>"POST", "User-Agent: SWViewer/1.3 (https://tools.wmflabs.org/swviewer; swviewer@tools.wmflabs.org) PHP / getGlobals.php"));
    $context = stream_context_create($options);

    if ($groups == "all")
        $groups = "apihighlimits-requestor|captcha-exempt|wmf-researcher|wmf-ops-monitoring|sysadmin|recursive-export|otrs-member|new-wikis-importer|global-interface-editor|global-flow-create|global-deleter|global-bot|staff|steward|global-sysop|global-rollbacker|abusefilter-helper|founder|ombudsman";
    $check = true;
    $agufrom = "";
    $cont = "";

    while($check == true) {
        $url = "https://meta.wikimedia.org/w/api.php?action=query&format=json&list=globalallusers&formatversion=2&utf8=1&agugroup=".$groups."&agulimit=100".$agufrom.$cont;
        $content = file_get_contents($url, false, $context);
        $json = json_decode($content, true);
        if (!isset($json["continue"])  || !isset($json["continue"]["agufrom"]) || !isset($json["continue"]["continue"]))
            $check = false;
        else {
            $agufrom = "&agufrom=".urlencode($json["continue"]["agufrom"]);
            $cont = "&continue=".$json["continue"]["continue"];
        }
        foreach($json["query"]["globalallusers"] as $user) {
            if (!in_array($user['name'], $GLOBALS['usersList']))
	        array_push($GLOBALS['usersList'], $user['name']);
        }
    }
}
?>