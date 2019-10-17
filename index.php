<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
# Redirect to https
if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' ||
   $_SERVER['HTTPS'] == 1) ||
   isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
   $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
{
   $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
   header('HTTP/1.1 301 Moved Permanently');
   header('Location: ' . $redirect);
   exit();
}
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>SWViewer</title>

        <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
        <link rel="pwa-setup" href="site.webmanifest">
        <link rel="mask-icon" href="img/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name="apple-mobile-web-app-title" content="SWViewer">
        <meta name="application-name" content="SWViewer">
        <meta name="author" content="Iluvatar, Ajbura, 1997kB">
        <meta name="description" content="App for viewing queue of edits on small wikis for SWMT">
        <meta name="keywords" content="SWMT">
        <meta name="msapplication-TileColor" content="#808d9f">
        <meta name="theme-color" content="#212121">
        <!-- AngularJS, jQuery, Bootstrap, Moment, pwacompat -->
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/angular.js/1.7.2/angular.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/angular-ui/0.4.0/angular-ui.min.js"></script>
        <!-- <script type="text/javascript" async src="//cdn.jsdelivr.net/npm/pwacompat@2.0.9/pwacompat.min.js"></script> -->
        <script async src="js/pwacompat.js"></script>
        <script src="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <!-- Fonts, Bootstrap, stylesheet-->
        <link rel="stylesheet" href="//tools-static.wmflabs.org/fontcdn/css?family=Roboto|Montserrat" type="text/css">
        <link rel="stylesheet" href="//tools-static.wmflabs.org/cdnjs/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="css/swv.css?v=1.0">
    </head>

<?php
# Callback errors
if (isset($_GET["error"])) {
    if ($_GET["error"] == "rights")
        echo "<div style='background-color: red;' align=center>Sorry, to use this application <a rel='noopener noreferrer' target='_blank' href='https://en.wikipedia.org/wiki/Wikipedia:Rollback'>local</a> or <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Global_rollback'>global</a> rollback is required.<br>If you have rollback right and see that error, then report about it on <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/SWViewer'>talk page</a>. Thanks!</div>";
    if ($_GET["error"] == "internal")
        echo "<div style='background-color: red;' align=center>Internal server error</div>";
exit();
}

# If user is not logged in, then show login layer
session_name( 'SWViewer' );
session_start();
if ((isset($_SESSION['tokenKey']) == false) or (isset($_SESSION['tokenSecret']) == false) or (isset($_SESSION['userName']) == false)) {
    session_write_close();
    echo "
        <noscript>
            <span style='color: red;'>JavaScript is not enabled!</span>
        </noscript>
        <div style='top: 0; bottom: 0; right: 0;left: 0; margin: auto; height: 200px; position: absolute;'>
            <div style='text-align: center;'><h3 style='margin-top: unset;'>Welcome!</h3></div>
            <div class='local' style='text-align:center; margin-top:15px;'>
                <div>To work in the system, you need to log in.</div>
                <div style='margin-top: 10px;' align=center>
                    <a id='abtn' href='https://tools.wmflabs.org/swviewer/php/oauth.php?action=start' class='btn btn-primary btn-lg oauth' style='width:255px;'>Authorization</a>
                </div>
                <div style='margin-top: 10px; text-align: left; width: 255px; margin: auto; margin-top: 10px;' align=center>
	            <span style='font-size: x-small;'>To use this application <a rel='noopener noreferrer' target='_blank' href='https://en.wikipedia.org/wiki/Wikipedia:Rollback'>local</a> or <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Global_rollback'>global</a> rollback is required.</span></div>
	        </div>
           </div>
        </div>
        <div style='position: absolute; bottom: 16px; width: 100%; text-align: center;'>
            <div style='width: 100%; text-align: center;'><a id='about-btn' style='cursor: pointer;'>About</a></div>
            <div style='width: 100%; text-align: center; font-size: x-small;'>Brought to you by <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/User:Iluvatar'>Iluvatar</a>, <a rel='noopener noreferrer' target='_blank' href='https://ajbura.github.io'>ajbura</a>, <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/User:1997kB'>1997kB</a></div>     
        </div>

    <div class='modal fade aboutForm' id='aboutForm' tabindex='-1' role='dialog' aria-labelledby='aboutFormLabel'>
        <div class='modal-dialog modal-lg' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                    <h4 class='modal-title' style='display: inline-block' id='aboutFormLabel'>About SWViewer</h4>
                </div>
                <div class='modal-body'>
                    <div class='about-frame' style='overflow: hidden;'>
                        <iframe class='full-screen' src='templates/about.html' style='border: none'></iframe>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('abtn').focus();
        document.getElementById('about-btn').onclick = function() {
            $('#aboutForm').modal('show');
        };
    </script>";
    exit(0);
}

# Check user is banned in SWV
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('SELECT name FROM user WHERE locked=1 AND name=:name');
$q->execute(array(':name' => $_SESSION["userName"]));
$result = $q->fetchAll();
$isLocked = count($result);

# User is banned
if ($isLocked !== 0) {
    echo "Access denied. Dev. code: b001.";
    $_SESSION = array();
    session_write_close();
    exit();
}

# User is not banned. Uodate stats: count of opens and date of last open ("offline users� in The Talk)
$q = $db->prepare('UPDATE user SET lastopen=null, openscount=openscount+1 WHERE name=:name');
$q->execute(array(':name' => $_SESSION["userName"]));

# Check talk token is not empty -- temp
$q = $db->prepare('SELECT token FROM user WHERE name=:name');
$q->execute(array(':name' => $_SESSION["userName"]));
$db = null;
$result = $q->fetch();
if (!isset($result[0]) || $result[0] == null || $result[0] == "" || $_SESSION['talkToken'] !== $result[0]) {
    echo '
    <script>
    alert("TalkToken is empty. Please re-login.");
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "php/oauth.php?action=unlogin", false);
    xhr.send();
    if (xhr.responseText == "Unlogin is done")
        window.open("https://tools.wmflabs.org/swviewer/", "_self");
    </script>
    ';
    exit();
}

$userSelf = $_SESSION["userName"];
$isGlobalModeAccess = false;
$isGlobal = false;
if ($_SESSION['mode'] == "global")
    $isGlobal = true;
else
    if (isset($_SESSION['accessGlobal']))
        if ($_SESSION['accessGlobal'] === "true")
            $isGlobalModeAccess = true;
session_write_close();
?>

<script>
var xhr = new XMLHttpRequest();
xhr.open('POST', "php/getSessionVars.php", false);
xhr.send();
var sess = JSON.parse(xhr.responseText);
if (!sess.hasOwnProperty("user") || !sess.hasOwnProperty("isGlobal") || !sess.hasOwnProperty("isGlobalModeAccess") || !sess.hasOwnProperty("local_wikis") || !sess.hasOwnProperty("talktoken") || sess.hasOwnProperty("error")) {
    alert("Something gone wrong. Please retry.");
    xhr.open("GET", "php/oauth.php?action=unlogin", false);
    xhr.send();
    if (xhr.responseText == "Unlogin is done")
        window.open("https://tools.wmflabs.org/swviewer/", "_self");
}

var userSelf = sess["user"];
var isGlobal = Boolean(sess["isGlobal"]);
var isGlobalModeAccess = Boolean(sess["isGlobalModeAccess"]);
var talktoken = sess["talktoken"]; // DO NOT GIVE TO ANYONE THIS TOKEN, OTHERWISE THE ATTACKER WILL CAN OPERATE AND SENDS MESSAGES UNDER YOUR NAME!
var local_wikis = [];
if (sess["local_wikis"] !== "")
    local_wikis = sess["local_wikis"].split(',');
</script>

<body  class="full-screen" id="mainapp-body">
<!-- Loading intro -->
<div id="loading">
    <div class="loading-icon">
        <svg version="1.1"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
            x="0px" y="0px" height="285px" viewBox="0 0 285 285" enable-background="new 0 0 285 285" xml:space="preserve">
        <path d="M285,142.2C285,63.7,221.3,0,142.8,0h-0.5C63.7,0,0,63.7,0,142.2v0.5C0,221.3,63.7,285,142.2,285h0.5
            c78.6,0,142.2-63.7,142.2-142.2V142.2z M133,191.4c0,4.9-3.1,9.2-7.8,10.5l-39.6,10.7c-6.9,1.9-13.5-3.3-13.5-10.5V83
            c0-7.1,6.8-12.3,13.7-10.5l39.5,10.7c4.7,1.3,7.8,5.6,7.8,10.5V191.4z M213,191.4c0,4.9-3.3,9.2-8.1,10.5l-40,10.7
            c-6.9,1.9-13.9-3.3-13.9-10.5V83c0-7.1,7-12.3,13.9-10.5l39.9,10.7c4.7,1.3,8.2,5.6,8.2,10.5V191.4z"/>
        </svg>
    </div>
    <div class="loading-text">SW Viewer</div>
</div>
<div id="angularapp" ng-app="swv" ng-controller="Queue">
<div class="base-container" id="app">
    <!-- Top panel (title & control buttons) -->
    <div class="main-header-container">
        <div class="header-text-container">
            <p class="app-title">SW Viewer</p>
        </div>

		<div id="control" class="control-container">
			<div id="back" ng-click="Back();" class="svg-btn-base back" title="Previous difference [Left square bracket]">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                <path fill="#424242" d="M180.1,248L9.6,134.4c-4.6-3-4.6-9.7,0-12.8L180.1,8c5.1-3.4,11.9,0.3,11.9,6.4l0,49.7l56.3,0
                    c4.2,0,7.7,3.4,7.7,7.7v112.6c0,4.2-3.4,7.7-7.7,7.7l-56.3,0l0,49.6C192,247.8,185.2,251.4,180.1,248z"/>
                </svg>
			</div>
			<div id="revert" ng-click="Revert();" class="svg-btn-base revert" title="Quick Rollback [r]">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                <path fill="#424242" d="M192,111.4L192,111.4c0,26.2-21.2,47.4-47.4,47.4h-55v-15.8c0-6.3-7.1-9.9-12.2-6.3L8.8,185.7
                    c-4.3,3.1-4.3,9.4,0,12.5l68.7,49.1c5.1,3.6,12.2,0,12.2-6.3v-18.3h55c61.5,0,111.4-49.9,111.4-111.4v0C256,49.9,206.1,0,144.6,0
                    l-23.5,0c-17.7,0-32,14.3-32,32v0c0,17.7,14.3,32,32,32h23.5C170.8,64,192,85.2,192,111.4z"/>
                </svg>
			</div>
			<div id="customRevertBtn" ng-click="customRevertSummary();" class="svg-btn-base customRevertBtn" title="Rollback with summary [y]">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                <g>
                    <path fill="#424242" d="M192,111.4L192,111.4c0,26.2-21.2,47.4-47.4,47.4h-55v-15.8c0-6.3-7.1-9.9-12.2-6.3L8.8,185.7
                        c-4.3,3.1-4.3,9.4,0,12.5l68.7,49.1c5.1,3.6,12.2,0,12.2-6.3v-18.3h55c61.5,0,111.4-49.9,111.4-111.4v0C256,49.9,206.1,0,144.6,0
                        l-23.5,0c-17.7,0-32,14.3-32,32v0c0,17.7,14.3,32,32,32h23.5C170.8,64,192,85.2,192,111.4z"/>
                    <path fill="#424242" d="M37.5,64L37.5,64c-17.7,0-32-14.3-32-32v0c0-17.7,14.3-32,32-32h0c17.7,0,32,14.3,32,32v0
                        C69.5,49.7,55.2,64,37.5,64z"/>
                </g>
                </svg>
			</div>
			<div id="editBtn" ng-click="checkEdit();" class="svg-btn-base doedit" title="Edit source [e]">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                <g>
                    <path fill="#424242" d="M127.8,7.7v48.6c0,4.2-3.4,7.7-7.7,7.7H71.6c-4.2,0-7.7,3.4-7.7,7.7v112.5c0,4.2,3.4,7.7,7.7,7.7h112.5
                        c4.2,0,7.7-3.4,7.7-7.7v-48.6c0-4.2,3.4-7.7,7.7-7.7H248c4.2,0,7.7,3.4,7.7,7.7V248c0,4.2-3.4,7.7-7.7,7.7H7.6
                        c-4.2,0-7.7-3.4-7.7-7.7L0,7.7C0,3.4,3.4,0,7.6,0l112.5,0C124.4,0,127.8,3.4,127.8,7.7z"/>
                    <g>
                        <polygon fill="#424242" points="207.9,25.3 128,105.2 128,128 150.4,128 230.4,47.9 		"/>
                        <path fill="#424242" d="M250.2,17.2L238.5,5.4c-3-3-7.9-3-10.9,0l-10.8,10.8l22.6,22.6L250.2,28C253.2,25,253.2,20.2,250.2,17.2z"/>
                    </g>
                </g>
                </svg>
			</div>
			<div id="browser" ng-click="browser();" class="svg-btn-base browser" title="Open in browser window [o]">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                <g>
                    <path fill="#424242" d="M128,7.7v48.6c0,4.2-3.4,7.7-7.7,7.7H71.7c-4.2,0-7.7,3.4-7.7,7.7v112.6c0,4.2,3.4,7.7,7.7,7.7h112.6
                        c4.2,0,7.7-3.4,7.7-7.7v-48.6c0-4.2,3.4-7.7,7.7-7.7h48.6c4.2,0,7.7,3.4,7.7,7.7v112.6c0,4.2-3.4,7.7-7.7,7.7H7.7
                        c-4.2,0-7.7-3.4-7.7-7.7L0,7.7C0,3.4,3.4,0,7.7,0l112.6,0C124.6,0,128,3.4,128,7.7z"/>
                    <path fill="#424242" d="M160,39.7v16.6c0,4.2,3.4,7.7,7.7,7.7h80.6c4.2,0,7.7-3.4,7.7-7.7V39.7c0-4.2-3.4-7.7-7.7-7.7h-80.6
                        C163.4,32,160,35.4,160,39.7z"/>
                    <path fill="#424242" d="M199.7,96h16.6c4.2,0,7.7-3.4,7.7-7.7V7.7c0-4.2-3.4-7.7-7.7-7.7l-16.6,0c-4.2,0-7.7,3.4-7.7,7.7v80.6
                        C192,92.6,195.4,96,199.7,96z"/>
                </g>
                </svg>
			</div>
		</div>


    </div>

    <!-- Left panel (Queue & settings / unlogin buttons) -->
    <div class="queue-buttons-block">
        <div class="queue" id="queue">
            <div id="edits-Queue" ng-repeat="edit in edits track by $index" onclick="queueClick()">
                <div class="queue-row" ng-style="editColor(edit)" ng-click="select(edit)">{{edit.wiki}}</div>
            </div>
        </div>
        <div class="queue-buttons">
            <div class="queue-buttons-container">
                <div id="btn-talk" class="btn-talk" title="Talk [t]">
                    <span class="badge badge-talk" style="background: none;" id="badge-talk">{{filteredUsersTalk.length}}</span>
                </div>
                <div id="btn-logs" class="btn-logs" title="Logs">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                        x="0px" y="0px" viewBox="0 0 80 100" enable-background="new 0 0 80 100" xml:space="preserve">
                    <path fill="#FFFFFF" d="M51.5,0H3C1.3,0,0,1.3,0,3v94c0,1.7,1.3,3,3,3h74c1.7,0,3-1.3,3-3V28.5c0-0.8-0.3-1.6-0.9-2.1L53.6,0.9
                        C53.1,0.3,52.3,0,51.5,0z M61,84H19c-1.7,0-3-1.3-3-3v-3.9c0-1.7,1.3-3,3-3h12.6c1.7,0,3-1.3,3-3V59c0-1.7-1.3-3-3-3H19
                        c-1.7,0-3-1.3-3-3v-4.6c0-1.7,1.3-3,3-3h12.6c1.7,0,3-1.3,3-3V30.3c0-1.7-1.3-3-3-3H19c-1.7,0-3-1.3-3-3V19c0-1.7,1.3-3,3-3h23.8
                        c1.7,0,3,1.3,3,3v12.2c0,1.7,1.3,3,3,3H61c1.7,0,3,1.3,3,3V81C64,82.7,62.7,84,61,84z"/>
                    </svg>
                </div>
                <div id="btn-settings" class="btn-settings" title="Settings and quick links [s]">
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                    <path fill="#BBBBBB" d="M256,143.4v-30.7c0-4.2-3.4-7.7-7.7-7.7h-21.8c-3.3,0-6.2-2.1-7.3-5.2c-1.7-5.1-4-10.9-6.6-16.2
                        c-1.5-3-1-6.6,1.4-9l15-14.9c3-3,3-7.9,0-10.9l-21.7-21.6c-3-3-7.9-3-10.9,0l-14.9,15c-2.4,2.4-6.1,2.9-9.2,1.3
                        c-5-2.7-10.1-4.9-15.9-6.6c-3.3-1-5.5-3.9-5.5-7.4V7.7c0-4.2-3.4-7.7-7.7-7.7h-30.7c-4.2,0-7.7,3.4-7.7,7.7v21.8
                        c0,3.4-2.3,6.4-5.6,7.4c-5.9,1.7-11.1,4-16.2,6.7c-3,1.6-6.7,1.1-9.1-1.3L59,27.2c-3-3-7.9-3-10.8,0L26.4,49c-3,3-3,7.9,0,10.9
                        l15,14.8c2.4,2.4,2.9,6,1.4,9c-2.7,5.3-4.9,11-6.6,16.1c-1.1,3.1-4,5.2-7.3,5.2H7.7c-4.2,0-7.7,3.4-7.7,7.7v30.7
                        c0,4.2,3.4,7.7,7.7,7.7h20.9c3.4,0,6.4,2.3,7.4,5.6c1.7,5.9,4,11.3,6.7,16.4c1.6,3,1.1,6.7-1.3,9.1l-15.1,15.2c-3,3-3,7.8,0,10.8
                        l21.8,21.8c3,3,7.8,3,10.8,0l15.2-15.1c2.4-2.4,6.1-2.9,9.1-1.3c5.1,2.7,10.3,5,16.2,6.7c3.3,1,5.5,3.9,5.5,7.4v20.6
                        c0,4.2,3.4,7.7,7.7,7.7h30.7c4.2,0,7.7-3.4,7.7-7.7v-20.6c0-3.4,2.3-6.4,5.5-7.4c5.8-1.7,10.9-4,15.9-6.7c3-1.6,6.7-1.1,9.2,1.3
                        l14.9,15c3,3,7.9,3,10.9,0l21.7-21.7c3-3,3-7.8,0-10.8L214,182.1c-2.4-2.4-2.9-6.1-1.3-9.1c2.7-5.1,5-10.5,6.7-16.4
                        c1-3.3,3.9-5.6,7.4-5.6h21.5C252.6,151,256,147.6,256,143.4z M177.7,128.6c0,27.6-22.4,50-50,50s-50-22.4-50-50v0
                        c0-27.6,22.4-50,50-50S177.7,101,177.7,128.6L177.7,128.6z"/>
                    </svg>
                </div>
                <div id="btn-drawer" class="btn-drawer" onclick="openDrawer()" style="position: relative;">
                    <span id="edits-count">{{edits.length}}</span>
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                    <g>
                        <path fill="#BBBBBB" d="M248.3,151H7.7c-4.2,0-7.7-3.4-7.7-7.7v-30.7c0-4.2,3.4-7.7,7.7-7.7h240.6c4.2,0,7.7,3.4,7.7,7.7v30.7
                            C256,147.6,252.6,151,248.3,151z"/>
                        <path fill="#BBBBBB" d="M248.3,244H7.7c-4.2,0-7.7-3.4-7.7-7.7v-30.7c0-4.2,3.4-7.7,7.7-7.7h240.6c4.2,0,7.7,3.4,7.7,7.7v30.7
                            C256,240.6,252.6,244,248.3,244z"/>
                        <path fill="#BBBBBB" d="M248.3,58.1H7.7c-4.2,0-7.7-3.4-7.7-7.7V19.7C0,15.4,3.4,12,7.7,12h240.6c4.2,0,7.7,3.4,7.7,7.7v30.7
                            C256,54.6,252.6,58.1,248.3,58.1z"/>
                    </g>
                    </svg>
                </div>
            </div>
        </div>
        <div class="next-diff" id="next-diff" ng-click='nextDiff()'>
            <div class="svg-btn-base">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 256 256" enable-background="new 0 0 256 256" xml:space="preserve">
                <path fill="#424242" d="M180.1,248L9.6,134.4c-4.6-3-4.6-9.7,0-12.8L180.1,8c5.1-3.4,11.9,0.3,11.9,6.4l0,49.7l56.3,0
                    c4.2,0,7.7,3.4,7.7,7.7v112.6c0,4.2-3.4,7.7-7.7,7.7l-56.3,0l0,49.6C192,247.8,185.2,251.4,180.1,248z"/>
                </svg>
            </div>
            <span>Fetching</span>
        </div>
    </div>
    <div class="frame-container">
	    <div id="description-container" class="description-container" style="display: none;">
            <div class="description">
                <div class="description-long" id="wiki"></div>
                <div class="description-long ns" id="ns"></div>
            </div>
            <div class="description">
                <div class="description-long" id="us" style="display:none;">User: <div id="userLinkSpec" style="display: inline" ng-click="openLink('diff');"></div></div>
                <div class="description-long description-long-right desc-title" id="tit"></div>
            </div>
            <div class="description">
                <div class="description-long description-long-comment desc-comment" id="com"></div>
           </div>
		</div>
	    <div class="diff-container">
            <iframe id='page-welcome' style='display: block;' class='full-screen frame-diff' title='Welcome page' src='templates/welcome.html'></iframe>
	        <iframe id='page' class='full-screen frame-diff' style='display: none;' title='Diff' sandbox='allow-same-origin' scrolling='no'></iframe>
	    </div>
	</div>
</div>

<!-- Popup forms -->
<div class="modal fade customRevert" id="customRevert" tabindex="-1" role="dialog" aria-labelledby="customRevertLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="display: inline-block" id="customRevertLabel">Custom revert</h4>
            </div>
            <div class="modal-body">
                <div id="summariesContainer" style="display: block">
                    <label style="display: inline-block;" for="credit">Reason:</label>
                    <input title="Reason" name="credit" id="credit" placeholder="Provide a reason."/>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default" style="margin-top: 8px;">
                        <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSummariesOne">
                            <label>Common Summaries:
                                <div id="warn-box" class="warn-box warn-off">
                                    <label id="check-label">Warn OFF</label>
                                </div>
                            </label>
                            <div class="panel-body">
                                <div class="queue" id="queue">
                                    <div class="panel-cr-reasons" ng-repeat="description in descriptions track by $index" onclick="queueClick()">
                                        <div ng-style="descriptionColor(description)" ng-click="selectDescription(description)">{{description.name}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="summariesFooter" class="modal-footer">
                <button type="button" class="btn btn-danger" id="btn-cr-u-apply" ng-click="Revert();">Revert</button>
            </div>
        </div>
    </div>
</div>

<div id="editForm" class="popup-base" style="display: none;">
	<div class="popup-header">
		<div class="popup-title">Source code</div>
		<div class="popup-control">
			<div id="editForm-close-btn" class="svg-btn-base" title="Close [esc]">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
				<path fill="#BBBBBB" d="M917.5,193.4l-86.9-86.9c-12-12-31.4-12-43.4,0L533.7,360c-12,12-31.4,12-43.4,0L236.8,106.5
					c-12-12-31.4-12-43.4,0l-86.9,86.9c-12,12-12,31.4,0,43.4L360,490.3c12,12,12,31.4,0,43.4L106.5,787.2c-12,12-12,31.4,0,43.4
					l86.9,86.9c12,12,31.4,12,43.4,0L490.3,664c12-12,31.4-12,43.4,0l253.5,253.5c12,12,31.4,12,43.4,0l86.9-86.9c12-12,12-31.4,0-43.4
					L664,533.7c-12-12-12-31.4,0-43.4l253.5-253.5C929.5,224.8,929.5,205.4,917.5,193.4z"/>
				</svg>
			</div>
			<div id="editForm-drawer-btn" class="popup-drawer-btn svg-btn-base">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
				<path fill="#BBBBBB" d="M993.3,0H607.5c-8.1,0-15.9,3.2-21.7,9L21.7,573.1c-12,12-12,31.4,0,43.4l385.8,385.8c12,12,31.4,12,43.4,0
					L1015,438.2c5.8-5.8,9-13.6,9-21.7V30.7C1024,13.7,1010.3,0,993.3,0z M768,364.8c-60.1,0-108.8-48.7-108.8-108.8
					c0-60.1,48.7-108.8,108.8-108.8S876.8,195.9,876.8,256C876.8,316.1,828.1,364.8,768,364.8z"/>
				</svg>
			</div>
		</div>
	</div>
		<div id="editFormBody" class="popup-body">
		<div class="popup-body-left">
			<div class="pbl-container">
				<div class="pbl-content" style="overflow: hidden;">
                	<textarea id="textpage" class="form-edit" title="Source code of page"></textarea>
                </div>
                <div class="pbl-input-container">
                    <input id="summaryedit" class="pbl-input" title="Summary" placeholder="Briefly describe your changes.">
                    <div id="editForm-save" class="pbl-input-send" ng-click="doEdit()" onclick="closeEditForm()" title="Publish changes">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
                        <path d="M1016.4,246.8L778.6,9c-5.8-5.8-13.6-9-21.8-9H30.8C13.8,0,0,13.8,0,30.8v963.9c0,17,13.8,30.8,30.8,30.8
                            h963.9c17,0,30.8-13.8,30.8-30.8V268.6C1025.4,260.4,1022.1,252.6,1016.4,246.8z M384.5,769c-70.8,0-128.2-57.4-128.2-128.2
                            s57.4-128.2,128.2-128.2s128.2,57.4,128.2,128.2S455.3,769,384.5,769z M644.3,353.8c0,17-13.8,30.8-30.8,30.8H162.4
                            c-17,0-30.8-13.8-30.8-30.8V158.9c0-17,13.8-30.8,30.8-30.8h451.2c17,0,30.8,13.8,30.8,30.8V353.8z"/>
                        </svg>
                    </div>
                </div>
            </div>
		</div>
		<div id="editForm-drawer-shadow" class="popup-body-right-shadow" style="display: none; background-color: rgba(0,0,0,0);"></div>
		<div id="editForm-drawer" class="popup-body-right" style="right: -50%; box-shadow: -8px 0 48px 0 rgba(0,0,0,0);">
			<div id="btn-group-delete" class="popup-layer" >
                            <div style="display: block; margin-bottom: 20px; margin-top: 5px" class="warn-box warn-on disabled" ng-click="requestsForm();">
                                <label>Requests</label>
                            </div>
                            <div class="tag-heading" style="margin-bottom: 5px">Tag for deletion:
                                <div id="warn-box-delete" class="warn-box warn-off" style="display: block; margin-bottom: 20px; margin-top: 5px">
                                    <label id="check-label-delete">Warn OFF</label>
                                </div>
                            </div>
          		<div id="delete-reason" ng-repeat="speedy in speedys track by $index" onclick="queueClick()">
                            <a ng-click="selectSpeedy(speedy)" ng-style="speedyColor(speedy)">{{speedy.name}}</a>
                        </div>
                    </div>
		</div>
	</div>
</div>

<div class="modal fade requestsForm" id="requestsForm" tabindex="-1" role="dialog" aria-labelledby="requestsFormLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="display: inline-block" id="requestsFormLabel">Requests</h4>
            </div>
            <div class="modal-body">

                <div class="panel panel-default" style="margin-top: 8px;">
                    <div style="width:100%; white-space: nowrap;">

                        <div style="display: inline-block; width: calc(100% / 2);" id="reportDiffsLocal">
                            <label for="request-block-text-local">Local</label>
                            <input type="text" id="reportHeaderLocal" /><br>
                            <textarea id="reportCommentLocal" style="display: block; width: 100%;"></textarea><br>
                            <button id="request-btn-block-send-l" ng-click="sendReportLocal()"; style="display:block">Send</button>
                        </div>

                        <div style="display: inline-block; width: calc(100% / 2);" id="reportDiffs">
                            <label for="request-block-text-global">Global</label>
                            <input type="text" id="reportHeader" /><br>
                            <textarea id="reportComment" style="display: block; width: 100%;"></textarea><br>
                            <button id="request-btn-block-send-g" ng-click="reqBlockG();" style="display:block">Send</button>
                        </div>
                       
                       <hr>

                       <div style="display: inline-block; width: calc(100% / 2);" id="protectDiffsLocal">
                            <label for="request-block-text-local">Local protect</label>
                            <input type="text" id="protectHeaderLocal" /><br>
                            <textarea id="protectCommentLocal" style="display: block; width: 100%;"></textarea><br>
                            <button id="request-btn-protect-send-l" ng-click="sendRequestProtect();" style="display:block">Send</button>
                       </div>

                       <div style="display: inline-block; width: calc(100% / 2);" id="othersDiffsGlobal">
                            <label for="request-block-text-local">Global miscellaneous</label>
                            <input type="text" id="othersHeaderGlobal" /><br>
                            <textarea id="othersCommentGlobal" style="display: block; width: 100%;"></textarea><br>
                            <button id="request-btn-others-send-g" ng-click="reqOthersG();" style="display:block">Send</button>
                       </div>

                   </div>
               </div>
           </div>
       </div>
   </div>
</div>


<!-- Main settings form -->
<div id="settings" class="popup-base" style="display: none;">
	<div class="popup-header">
		<p class="popup-title">Settings</p>
		<div class="popup-control">
            <div id="btn-unlogin" class="svg-btn-base" title="Sign out [u]">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                    x="0px" y="0px" viewBox="0 0 90.3 97" enable-background="new 0 0 90.3 97" xml:space="preserve">
                <g>
                    <path fill="#FFFFFF" d="M48.1,53.3h-6.3c-1.6,0-2.9-1.3-2.9-2.9V2.9c0-1.6,1.3-2.9,2.9-2.9h6.3C49.7,0,51,1.3,51,2.9v47.5
                        C51,52,49.7,53.3,48.1,53.3z"/>
                    <path fill="#FFFFFF" d="M76.9,21.3c-3.3-3.2-7.2-6-11.4-8.1c-1.8-0.9-3.9,0.4-3.9,2.4v12.5c0,0.8,0.4,1.6,1.1,2.1
                        c1,0.7,1.9,1.5,2.8,2.3c5.2,5.1,8.7,12.2,8.7,20.1C74.1,68.3,61.3,81,45.4,81c-8,0-15.1-3.2-20.3-8.3c-5.2-5.1-8.4-12.2-8.4-20.1
                        c0-7.8,2.7-14.9,8-20.1c0.8-0.8,1.7-1.6,2.7-2.3c0.7-0.5,1-1.3,1-2.1V15.4c0-2-2.1-3.3-3.9-2.4C10.1,20.4,0,35.4,0,52.6
                        C0,64.9,5.3,76,13.4,84c8.2,8,19.5,13,32,13s23.7-5,31.9-13c8.2-8,13-19.1,13-31.4S85,29.3,76.9,21.3z"/>
                </g>
                </svg>
            </div>
			<div id="settings-close-btn" class="svg-btn-base" title="Close [esc]">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
				<path fill="#BBBBBB" d="M917.5,193.4l-86.9-86.9c-12-12-31.4-12-43.4,0L533.7,360c-12,12-31.4,12-43.4,0L236.8,106.5
					c-12-12-31.4-12-43.4,0l-86.9,86.9c-12,12-12,31.4,0,43.4L360,490.3c12,12,12,31.4,0,43.4L106.5,787.2c-12,12-12,31.4,0,43.4
					l86.9,86.9c12,12,31.4,12,43.4,0L490.3,664c12-12,31.4-12,43.4,0l253.5,253.5c12,12,31.4,12,43.4,0l86.9-86.9c12-12,12-31.4,0-43.4
					L664,533.7c-12-12-12-31.4,0-43.4l253.5-253.5C929.5,224.8,929.5,205.4,917.5,193.4z"/>
				</svg>
			</div>
			<div id="settings-drawer-btn" class="popup-drawer-btn svg-btn-base">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                    x="0px" y="0px" viewBox="0 0 100 84" enable-background="new 0 0 100 84" xml:space="preserve">
                <g>
                    <path fill="#FFFFFF" d="M97,51H3c-1.7,0-3-1.3-3-3V36c0-1.7,1.3-3,3-3h94c1.7,0,3,1.3,3,3v12C100,49.7,98.7,51,97,51z"/>
                    <path fill="#FFFFFF" d="M97,84H3c-1.7,0-3-1.3-3-3V69c0-1.7,1.3-3,3-3h94c1.7,0,3,1.3,3,3v12C100,82.7,98.7,84,97,84z"/>
                    <path fill="#FFFFFF" d="M97,18H3c-1.7,0-3-1.3-3-3V3c0-1.7,1.3-3,3-3h94c1.7,0,3,1.3,3,3v12C100,16.7,98.7,18,97,18z"/>
                </g>
                </svg>
			</div>
		</div>
	</div>
	<div class="popup-body">
		<div class="popup-body-left">
			<div class="popup-layer">
				<div>
                    <div class="i-select-base">
                        <div class="i-select-lb-container">
                            <div class="i-select-lable-container">Theme</div>
                            <div class="i-select-container">
                                <select id="themeSelector">
                                </select>
                            </div>
                        </div>
                        <div class="i-select-disc-container">Change theme.</div>
                    </div>
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">Bottom-up</div>
                            <div class="i-btn-container">
                                <div id="bottom-up-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Show edits from bottom to up direction in queue.</div>
                    </div>
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">Sound</div>
                            <div class="i-btn-container">
                                <div id="sound-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Turn new edit load sound.</div>
                    </div>
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">Registered</div>
                            <div class="i-btn-container">
                                <div id="registered-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Enable edits from registered users.</div>
                    </div>
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">New pages</div>
                            <div class="i-btn-container">
                                <div id="new-pages-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Enable new page creations.</div>
                    </div>
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">Only new pages</div>
                            <div class="i-btn-container">
                                <div id="onlynew-pages-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Enable only new page creations.</div>
                    </div>
                    <?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">Small Wikis</div>
                            <div class="i-btn-container">
                                <div id="small-wikis-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Enable edits from small wikis.</div>
                    </div>
                    <div class="i-btn-base">
                        <div class="i-btn-lb-container">
                            <div class="i-btn-lable-container">Less then 300 users</div>
                            <div class="i-btn-container">
                                <div id="lt-300-btn" class="i-btn-oval" style="padding-left: 2.5px;">
                                    <div class="i-btn-circle"></div>
                                </div>
                            </div>
                        </div>
                        <div class="i-btn-disc-container">Enable edits from wikis with less then 300 active users.</div>
                    </div>
                    ';}?>
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input-lable-container">Edits limit</div>
                    		<div class="i-input-container">
                    			<input id="max-edits" name="max-edits" placeholder="Max edits">
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">Number of edits after which edits of user will be whitelisted.</div>
                    </div>
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input-lable-container">Days limit</div>
                    		<div class="i-input-container">
                    			<input id="max-days" name="max-days" placeholder="Max days">
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">Account age in days after which edits of user will be whitelisted.</div>
                    </div>
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input-lable-container">Queue limit</div>
                    		<div class="i-input-container">
                    			<input id="max-queue" name="max-queue" placeholder="No limit">
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">Max count of edits allowed to load in queue.</div>
                    </div>
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input2-lable-container">Namespace filter</div>
                    		<div class="i-input2-container">
                    			<div id="btn-delete-ns" class="minus-input">-</div>
                    			<input id="ns-input" name="" placeholder="Enter">
                    			<div id="btn-add-ns" class="plus-input">+</div>
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">
                    		Add <a style="display: inline;" href="https://en.wikipedia.org/wiki/Help:MediaWiki_namespace" target="_blank">namespace</a> to filter edits in queue.
                    		<ul id="nsList" class="i-input-disc-list"></ul>
                    	</div>
                    </div>
                    <?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input2-lable-container">Custom wikis</div>
                    		<div class="i-input2-container">
                    			<div id="btn-bl-p-delete" class="minus-input">-</div>
                    			<input id="bl-p" name="bl-p" placeholder="Enter">
                    			<div id="btn-bl-p-add" class="plus-input">+</div>
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">
                    		Add your home-wiki or wikis which are not in small wikis list.
                    		<ul id="blareap" class="i-input-disc-list"></ul>
                    	</div>
                    </div>
                    ';}?>
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input2-lable-container">Users whitelist</div>
                    		<div class="i-input2-container">
                    			<div id="btn-wl-u-delete" class="minus-input">-</div>
                    			<input id="wladdu" name="wladdu" placeholder="Enter">
                    			<div id="btn-wl-u-add" class="plus-input">+</div>
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">
                    		Add users to skip their edits from queue.
                    		<ul id="wlareau" class="i-input-disc-list"></ul>
                    	</div>
                    </div>
                    <div class="i-input-base">
                    	<div class="i-input-li-container">
                    		<div class="i-input2-lable-container">Wikis whitelist</div>
                    		<div class="i-input2-container">
                    			<div id="btn-wl-p-delete" class="minus-input">-</div>
                    			<input id="wladdp" name="wladdp" placeholder="Enter">
                    			<div id="btn-wl-p-add" class="plus-input">+</div>
                    		</div>
                    	</div>
                    	<div class="i-input-disc-container">
                    		Add wikis to skip their edits from queue.
                    		<ul id="wlareap" class="i-input-disc-list"></ul>
                    	</div>
                    </div>
		        </div>
		    </div>
		</div>
        <div id="settings-drawer-shadow" class="popup-body-right-shadow" style="display: none; background-color: rgba(0,0,0,0);"></div>
		<div id="settings-drawer" class="popup-body-right" style="right: -50%; box-shadow: -8px 0 48px 0 rgba(0,0,0,0);">
			<div class="popup-layer">
	            <label>Info</label>
	            <a id="luxo" href='https://tools.wmflabs.org/guc/?by=date&user={{selected.user}}' rel='noopener noreferrer'
	               target='_blank'>Global contribs</a>
	            <a href='https://tools.wmflabs.org/rangecontrib/?wiki=' rel='noopener noreferrer' target='_blank'>Range
	                contribs</a>
	            <a href='https://meta.wikimedia.org/wiki/Special:CentralAuth?target={{selected.user}}'
	               rel='noopener noreferrer' target='_blank'>SUL</a><br>
	            <label>Reports</label>
	            <a href='https://meta.wikimedia.org/wiki/Meta:Requests_for_help_from_a_sysop_or_bureaucrat'
	               rel='noopener noreferrer' target='_blank'>Vandalism on Meta</a>
	            <a href='https://meta.wikimedia.org/wiki/Steward_requests/Miscellaneous' rel='noopener noreferrer'
	               target='_blank'>Steward request (Miscellaneous)</a><br>
	            <label>Scripts, templates</label>
	            <a href='https://meta.wikimedia.org/wiki/User:Hoo_man/Scripts/Tagger' rel='noopener noreferrer'
	               target='_blank'>Tagger</a>
	            <a href='https://meta.wikimedia.org/wiki/User:Syum90/Warning_templates' rel='noopener noreferrer'
	               target='_blank'>Warning templates</a><br>
	            <label>Translators</label>
	            <a href='https://translate.google.com/#auto/en/' rel='noopener noreferrer' target='_blank'>Google
	                translator</a>
	            <a href='https://translate.yandex.com/' rel='noopener noreferrer' target='_blank'>Yandex
	                translator</a>
	            <a href='http://www.online-translator.com' rel='noopener noreferrer' target='_blank'>Promt
	                translator</a>
	            <a href='https://www.bing.com/translator' rel='noopener noreferrer' target='_blank'>Bing
	                translator</a>
	            <a href='https://www.deepl.com/en/translator' rel='noopener noreferrer' target='_blank'>DeepL
	                translator</a><br>
	            <label>IRC</label>
	            <a href='http://webchat.freenode.net/?channels=%23countervandalism%2C%23cvn-sw'
	               rel='noopener noreferrer' target='_blank'>Join to IRC</a><?php if ($userSelf == "Ajbura" || $userSelf == "Iluvatar" || $userSelf == "1997kB") echo "<br>
	            <label>Control SWV</label>
	            <a href='https://tools.wmflabs.org/swviewer/php/control.php'
	               rel='noopener noreferrer' target='_blank'>Control panel</a>";?>
          	</div>
		</div>
	</div>
</div>

<!-- Talk page form -->

<div id="talkForm" class="popup-base" style="display: none;">
	<div class="popup-header">
		<div class="popup-title">Talk</div>
		<div class="popup-control">
			<div id="talk-close-btn" class="svg-btn-base" title="Close [esc]">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
				<path fill="#BBBBBB" d="M917.5,193.4l-86.9-86.9c-12-12-31.4-12-43.4,0L533.7,360c-12,12-31.4,12-43.4,0L236.8,106.5
					c-12-12-31.4-12-43.4,0l-86.9,86.9c-12,12-12,31.4,0,43.4L360,490.3c12,12,12,31.4,0,43.4L106.5,787.2c-12,12-12,31.4,0,43.4
					l86.9,86.9c12,12,31.4,12,43.4,0L490.3,664c12-12,31.4-12,43.4,0l253.5,253.5c12,12,31.4,12,43.4,0l86.9-86.9c12-12,12-31.4,0-43.4
					L664,533.7c-12-12-12-31.4,0-43.4l253.5-253.5C929.5,224.8,929.5,205.4,917.5,193.4z"/>
				</svg>
			</div>
			<div id="talk-drawer-btn" class="popup-drawer-btn svg-btn-base">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                    x="0px" y="0px" viewBox="0 0 101 73.3" enable-background="new 0 0 101 73.3" xml:space="preserve">
                <g>
                    <g>
                        <path fill="#FFFFFF" d="M101.5,77.3v6.8H81v-6.4c0-13.6-7.1-25.6-17.8-32.4h6.3C87.2,45.3,101.5,59.7,101.5,77.3z"/>
                    </g>
                    <g>
                        <path fill="#FFFFFF" d="M81.5,17.5C81.5,27.2,73.7,35,64,35c-3.1,0-6.1-0.8-8.6-2.3c3.5-4.1,5.6-9.4,5.6-15.2S58.9,6.4,55.4,2.3
                            C57.9,0.8,60.9,0,64,0C73.7,0,81.5,7.8,81.5,17.5z"/>
                    </g>
                    <path fill="#FFFFFF" d="M75,77.3v6.8H0v-6.8c0-17.7,14.3-32,32-32h11C60.7,45.3,75,59.7,75,77.3z"/>
                    <circle fill="#FFFFFF" cx="37.5" cy="17.5" r="17.5"/>
                </g>
                </svg>
			</div>
		</div>
	</div>
	<div class="popup-body">
		<div class="popup-body-left">
            <div class="pbl-container" style="padding-left: 0; padding-right: 0;">
            	<div id="talk-content" class="pbl-content" style="padding-top: 11px; padding-left: 16px; padding-right: 16px;">
                	<div id="form-talk" class="talk-list"></div>
                </div>
                <div class="pbl-input-container" style="padding: 0 16px;">
                    <input id="phrase-send-talk" class="pbl-input" title="Text to sent" max-length="600" placeholder="What's on your mind?">
                    <div id="btn-send-talk" class="pbl-input-send" title="Send">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
                        <path d="M0,49.7v353c0,15.1,10.9,27.9,25.8,30.3l299.8,48.7c34.4,5.6,34.4,55,0,60.6L25.8,591
                            C10.9,593.4,0,606.3,0,621.3v353c0,22.8,24,37.7,44.4,27.5l924.7-462.3c22.6-11.3,22.6-43.6,0-54.9L44.4,22.2C24,12,0,26.8,0,49.7z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
		</div>
        <div id="talk-drawer-shadow" class="popup-body-right-shadow" style="display: block; background-color: rgba(0,0,0,.05);"></div>
		<div id="talk-drawer" class="popup-body-right" style="right: 0px; box-shadow: -8px 0 48px 0 rgba(0,0,0,.2);">
			<div id="users-talk" class="popup-layer">
                <div class="user-container" ng-repeat="talkUser in users|unique: talkUser as filteredUsersTalk">
                    <div class="user-talk" ng-click="selectTalkUsers(talkUser)">{{talkUser}}</div>
                    <a class="user-talk-CA" href="https://meta.wikimedia.org/wiki/Special:CentralAuth/{{talkUser}}" target="_blank">CA</a>
                </div>
                <div ng-repeat="talkUserOffline in offlineUsers track by $index">
                    <div class="user-talk" style="color: gray">{{talkUserOffline}}</div>
                </div>
            </div>
		</div>
	</div>
</div>


<!------- Logs popup ------>
<div id="logs" class="popup-base" style="display: none;">
	<div class="popup-header">
		<div class="popup-title">Logs</div>
		<div class="popup-control">
            <div id="logs-refresh-btn" class="svg-btn-base" title="Reload">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                    x="0px" y="0px" viewBox="0 0 84 95.9" enable-background="new 0 0 84 95.9" xml:space="preserve">
                <path fill="#FFFFFF" d="M42.1,11.9V3c0-2.7-3.2-4-5.1-2.1L20.2,17.6c-1.2,1.2-1.2,3.1,0,4.2L37,38.6c1.9,1.9,5.1,0.6,5.1-2.1v-8.6
                    c15.3,0.1,27.5,13.4,25.7,29C66.5,68.8,56.9,78.4,45,79.7c-15.7,1.7-29-10.5-29-25.8c0-3.8,0.8-7.5,2.4-10.8
                    c0.5-1.1,0.3-2.5-0.6-3.4l-7.4-7.4c-1.4-1.4-3.7-1.1-4.7,0.6C1.8,39.4-0.2,47,0,55.1C0.7,77.1,18.5,95,40.4,95.9
                    C64.3,96.7,84,77.6,84,53.9C84,30.7,65.3,12,42.1,11.9z"/>
                </svg>
            </div>
			<div id="logs-close-btn" class="svg-btn-base" title="Close [esc]">
				<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					 viewBox="0 0 1024 1024" enable-background="new 0 0 1024 1024" xml:space="preserve">
				<path fill="#BBBBBB" d="M917.5,193.4l-86.9-86.9c-12-12-31.4-12-43.4,0L533.7,360c-12,12-31.4,12-43.4,0L236.8,106.5
					c-12-12-31.4-12-43.4,0l-86.9,86.9c-12,12-12,31.4,0,43.4L360,490.3c12,12,12,31.4,0,43.4L106.5,787.2c-12,12-12,31.4,0,43.4
					l86.9,86.9c12,12,31.4,12,43.4,0L490.3,664c12-12,31.4-12,43.4,0l253.5,253.5c12,12,31.4,12,43.4,0l86.9-86.9c12-12,12-31.4,0-43.4
					L664,533.7c-12-12-12-31.4,0-43.4l253.5-253.5C929.5,224.8,929.5,205.4,917.5,193.4z"/>
				</svg>
            </div>
		</div>
	</div>
	<div class="popup-body">
        <div class="pbl-container" style="padding-left: 0; padding-right: 0;">
            <div class="pbl-content" style="padding-left: 16px; padding-right: 16px;">
                <div id="logsBox"></div>
                <div class="log-box-btn-container">
                    <button id="prevLogs" style="display: none;">Previous</button>
                    <button id="nextLogs" style="display: none;">Next</button>
                </div>
            </div>
            <div class="pbl-input-container" style="padding: 0 16px; position: relative;">
                <select id="actionSelector">
                    <option value="">All action</option>
                    <option value="rollback">Rollback</option>
                    <option value="delete">Delete</option>
                    <option value="edit">Edit</option>
                    <option value="warn">Warn</option>
                    <option value="report">Report</option>
                </select>
                <input id="search-input" class="pbl-input" style="padding-left: 90px;" title="search logs" max-length="600" placeholder="Search for user or wiki.">
                <div id="btn-searchLogs" class="pbl-input-send" title="Search">
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"
                        x="0px" y="0px" viewBox="0 0 98.9 98.5" enable-background="new 0 0 98.9 98.5"xml:space="preserve">
                    <path d="M71.9,65.8L71.9,65.8l-5-5C72,54.4,75,46.3,75,37.5C75,16.7,57.6-0.4,36.8,0C15.6,0.4-1.3,18.4,0.1,40
                        c1.2,18.6,16.2,33.6,34.8,34.9c10,0.7,19.3-2.6,26.4-8.4l5,5l0,0c-0.9,0.9-0.9,2.3,0,3.2l23,23c1.2,1.2,3.1,1.2,4.2,0L98,93
                        c1.2-1.2,1.2-3.1,0-4.2l-23-23C74.2,64.9,72.8,64.9,71.9,65.8z M37.5,57C26.7,57,18,48.3,18,37.5S26.7,18,37.5,18S57,26.7,57,37.5
                        S48.3,57,37.5,57z"/>
                    </svg>
                </div>
            </div>
        </div>
	</div>
</div>
</div>


<!-- Scripts -->
<script>
var diffstart, diffend, newstart, newend, startstring, endstring, starterror, enderror, config;
var wlistu = [];
var wlistp = [];
var global = [];
var vandals = [];
var suspects = [];
var customlist = [];
var sandboxlist = {};
var offlineUsers = [];
var nsList = { 0: "Main", 1: "Talk", 2: "User", 3: "User talk", 4: "Project", 5: "Project talk", 6: "File", 7: "File talk", 10: "Template", 11: "Template talk", 12: "Help", 13: "Help talk", 14: "Category", 15: "Category talk", 100: "Portal", 101: "Portal talk", 108: "Book", 109: "Book talk", 118: "Draft", 119: "Draft talk", 446: "Education program", 447: "Education program talk", 710: "TimedText", 711: "TimedText talk", 828: "Module", 828: "Module talk"}
var nsList2 = [];
var countqueue = 0;
var regdays = 5;
var countedits = 100;
var isSound = false;
var diffSound = new Audio("sounds/clap.mp3");
var messageSound;
var privateMessageSound;
firstClick = false;

document.getElementById("mainapp-body").onclick = function() {
    if (firstClick === false) {
        firstClick = true;
        messageSound = new Audio("sounds/message.mp3");
        privateMessageSound = new Audio("sounds/privateMessage.mp3");
        messageSound.load();
        privateMessageSound.load();
    }
};

var xhr = new XMLHttpRequest();

xhr.open('POST', "php/getSandbox.php", false);
xhr.send();
if (xhr.responseText == "Invalid request")
    location.reload();
var sandbox  = xhr.responseText;
sandbox = JSON.parse(sandbox);
for(var sb in sandbox["entities"]["Q3938"]["sitelinks"]) {
    if (sandbox["entities"]["Q3938"]["sitelinks"].hasOwnProperty(sb)) {
        sandboxlist[sandbox["entities"]["Q3938"]["sitelinks"][sb]["site"]] = sandbox["entities"]["Q3938"]["sitelinks"][sb]["title"];
    }
}
addSandbox(sandboxlist, "simplewiki", "Wikipedia:Introduction");

xhr.open('GET', "php/settings.php?action=get&query=all", false);
xhr.send();
// Bug with session on Safari browser
if (xhr.responseText == "Invalid request")
    location.reload();
var settingslist  = xhr.responseText;
settingslist = JSON.parse(settingslist);

<?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo "
if (settingslist['swmt'] !== null && (typeof settingslist['swmt'] !== 'undefined') && settingslist['swmt'] !== '')
    if ((settingslist['swmt'] === '1' || settingslist['swmt'] === '2') && isGlobal === true)
        toggleIBtn('small-wikis-btn', false);
    if (settingslist['swmt'] === '2' && isGlobalModeAccess === true) {
        toggleIBtn('small-wikis-btn', false);
}

if (settingslist['users'] !== null && (typeof settingslist['users'] !== 'undefined') && settingslist['users'] !== '') {
    if ((settingslist['users'] === '1' || settingslist['users'] === '2') && isGlobal === true)
        toggleIBtn('lt-300-btn', false);
    if (settingslist['users'] == '2' && isGlobalModeAccess === true)
        toggleIBtn('lt-300-btn', false);
}
"; } ?>

if (settingslist['registered'] !== null && (typeof settingslist['registered'] !== "undefined") && settingslist['regustered'] !== "") {
    if (settingslist['registered'] === "1")
        toggleIBtn("registered-btn", false);
}

if (settingslist['new'] !== null && (typeof settingslist['new'] !== "undefined") && settingslist['new'] !== "") {
    if (settingslist['new'] === "1")
        toggleIBtn("new-pages-btn", false);
}

if (settingslist['onlynew'] !== null && (typeof settingslist['onlynew'] !== "undefined") && settingslist['onlynew'] !== "") {
    if (settingslist['onlynew'] === "1")
        toggleIBtn("onlynew-pages-btn", false);
}

if (settingslist['direction'] !== null && (typeof settingslist['direction'] !== "undefined") && settingslist['direction'] !== "") {
    if (settingslist['direction'] === "1") {
        document.getElementById("queue").setAttribute("style", "display:flex; flex-direction:column-reverse");
        toggleIBtn("bottom-up-btn", false);
    }
}

if (settingslist['sound'] !== null && (typeof settingslist['sound'] !== "undefined") && settingslist['sound'] !== "") {
    if (settingslist['sound'] === "1") {
        isSound = true;
        toggleIBtn("sound-btn", false);
    }
}

if (settingslist['editcount'] !== null && (typeof settingslist['editcount'] !== "undefined") && settingslist['editcount'] !== "") {
    countedits = settingslist['editcount'];
    document.getElementById("max-edits").value = countedits;
}

if (settingslist['countqueue'] !== null && (typeof settingslist['countqueue'] !== "undefined") && settingslist['countqueue'] !== "" && settingslist['countqueue'] !== "0") {
    countqueue = settingslist['countqueue'];
    document.getElementById("max-queue").value = countqueue;
}

if (settingslist['regdays'] !== null && (typeof settingslist['regdays'] !== "undefined") && settingslist['regdays'] !== "") {
    regdays = settingslist['regdays'];
    document.getElementById("max-days").value = regdays;
}

if (settingslist['namespaces'] !== null && (typeof settingslist['namespaces'] !== "undefined") && settingslist['namespaces'] !== "") {
    nsList2 = settingslist['namespaces'].split(',');
    nsList2.forEach(function(val) {
        if (typeof nsList[val] !== "undefined")
            val = nsList[val];
        else
            val = "Other (" + val + ")";
        var ul = document.getElementById("nsList");
        var li = document.createElement('li');
        li.appendChild(createChipCross('btn-delete-ns'));
        li.appendChild(document.createTextNode(val));
        ul.appendChild(li);
    });
}

if (settingslist['wlprojects'] !== null && (typeof settingslist['wlprojects'] !== "undefined") && settingslist['wlprojects'] !== "") {
    wlistp = settingslist['wlprojects'].split(',');
    wlistp.forEach(function(val) {
        var ul = document.getElementById("wlareap");
        var li = document.createElement('li');
        li.appendChild(createChipCross('btn-wl-p-delete'));
        li.appendChild(document.createTextNode(val));
        ul.appendChild(li);
    });
}

if (settingslist['wlusers'] !== null && (typeof settingslist['wlusers'] !== "undefined") && settingslist['wlusers'] !== "") {
    wlistu = settingslist['wlusers'].split(',');
    wlistu.forEach(function(val) {
        var ul = document.getElementById("wlareau");
        var li = document.createElement('li');
        li.appendChild(createChipCross('btn-wl-u-delete'));
        li.appendChild(document.createTextNode(val));
        ul.appendChild(li);
    });
}

<?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo "
if (settingslist['blprojects'] !== null && (typeof settingslist['blprojects'] !== 'undefined') && settingslist['blprojects'] !== '') {
    customlist = settingslist['blprojects'].split(',');
    customlist.forEach(function(val) {
        var ul = document.getElementById('blareap');
        var li = document.createElement('li');
        li.appendChild(createChipCross('btn-bl-p-delete'));
        li.appendChild(document.createTextNode(val));
        ul.appendChild(li);
    });
}
"; } ?>

var globalFileCheck = true;
try {
    xhr.open('POST', "lists/globalUsers.txt", false);
    xhr.send();
    global = xhr.responseText.slice(0, -1).split(",");
}
catch(e) {
    globalFileCheck = false;
}
if (global.length < 5)
    globalFileCheck = false;
if (globalFileCheck === false) {
    xhr.open('POST', "php/getGlobals.php", false);
    xhr.send();
    global = xhr.responseText.slice(0, -1).split(",");
}

xhr.open('POST', "php/getConfig.php", false);
xhr.send();
if (xhr.responseText == "Invalid request")
    location.reload();
config = JSON.parse(xhr.responseText);

xhr.open('POST', "php/getOfflineUsers.php", false);
xhr.send();
offlineUsers = JSON.parse(xhr.responseText);

xhr.open('POST', "templates/diffStart.html", false);
xhr.send();
diffstart = xhr.responseText;

xhr.open('POST', "templates/diffEnd.html", false);
xhr.send();
diffend = xhr.responseText;

xhr.open('POST', "templates/newStart.html", false);
xhr.send();
newstart = xhr.responseText;

xhr.open('POST', "templates/newEnd.html", false);
xhr.send();
newend = xhr.responseText;

xhr.open('POST', "templates/newStringStart.html", false);
xhr.send();
startstring = xhr.responseText;

xhr.open('POST', "templates/newStringEnd.html", false);
xhr.send();
endstring = xhr.responseText;

xhr.open('POST', "templates/errorStart.html", false);
xhr.send();
starterror = xhr.responseText;

xhr.open('POST', "templates/errorEnd.html", false);
xhr.send();
enderror = xhr.responseText;

const THEME = {
    "Default": {
        TYPE: 1,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#212121', BG_LOW_PRIMARY: '#313131',
        BG_SECONDARY: '#ffffff', BG_LOW_SECONDARY: '#ffffff',
        TC_PRIMARY: '#ffffff', TC_LOW_PRIMARY: '#bbbbbb',
        TC_SECONDARY: '#212121', TC_LOW_SECONDARY: '#424242', TC_LOW_SECONDARY_GLOBAL: '#000000',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(255,255,255,.05)', BG_SECONDARY_HOVER: 'rgba(0,0,0,.05)',
        PRIMARY_BORDER: 'rgba(255,255,255,.1)', SECONDARY_BORDER: 'rgba(0,0,0,.1)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #212121; --bg-lowPrimary: #313131; --bg-secondary: #ffffff; --bg-lowSecondary: #ffffff; --tc-primary: #ffffff; --tc-lowPrimary: #bbbbbb; --tc-secondary: #212121; --tc-lowSecondary: #424242; --tc-lowSecondaryGlobal: #000000; --link-color: #337ab7; --bg-primary-hover: rgba(255,255,255,.05); --bg-secondary-hover: rgba(0,0,0,.05); --primary-border: rgba(255,255,255,.1); --secondary-border: rgba(0,0,0,.1);}',
    },
    "Light": {
        TYPE: 1,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#eeeeee', BG_LOW_PRIMARY: '#eeeeee',
        BG_SECONDARY: '#ffffff', BG_LOW_SECONDARY: '#ffffff',
        TC_PRIMARY: '#212121', TC_LOW_PRIMARY: '#424242',
        TC_SECONDARY: '#212121', TC_LOW_SECONDARY: '#424242', TC_LOW_SECONDARY_GLOBAL: '#000000',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(0,0,0,.05)', BG_SECONDARY_HOVER: 'rgba(0,0,0,.05)',
        PRIMARY_BORDER: 'rgba(0,0,0,.1)', SECONDARY_BORDER: 'rgba(0,0,0,.1)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #eeeeee; --bg-lowPrimary: #eeeeee; --bg-secondary: #ffffff; --bg-lowSecondary: #ffffff; --tc-primary: #212121; --tc-lowPrimary: #424242; --tc-secondary: #212121; --tc-lowSecondary: #424242; --tc-lowSecondaryGlobal: #000000; --link-color: #337ab7; --bg-primary-hover: rgba(0,0,0,.05); --bg-secondary-hover: rgba(0,0,0,.05); --primary-border: rgba(0,0,0,.1); --secondary-border: rgba(0,0,0,.1);}',
    },
    "Dark": {
        TYPE: 0,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#0b0b12', BG_LOW_PRIMARY: '#0b0b12',
        BG_SECONDARY: '#0f0f19', BG_LOW_SECONDARY: '#0f0f19',
        TC_PRIMARY: '#ffffff', TC_LOW_PRIMARY: '#bbbbbb',
        TC_SECONDARY: '#ffffff', TC_LOW_SECONDARY: '#bbbbbb', TC_LOW_SECONDARY_GLOBAL: '#c3c3c3',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(255,255,255,.05)', BG_SECONDARY_HOVER: 'rgba(255,255,255,.05)',
        PRIMARY_BORDER: 'rgba(255,255,255,.05)', SECONDARY_BORDER: 'rgba(255,255,255,.05)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #0b0b12; --bg-lowPrimary: #0b0b12; --bg-secondary: #0f0f19; --bg-lowSecondary: #0f0f19; --tc-primary: #ffffff; --tc-lowPrimary: #bbbbbb; --tc-secondary: #ffffff; --tc-lowSecondary: #bbbbbb; --tc-lowSecondaryGlobal: #c3c3c3; --link-color: #337ab7; --bg-primary-hover: rgba(255,255,255,.05); --bg-secondary-hover: rgba(255,255,255,.05); --primary-border: rgba(255,255,255,.05); --secondary-border: rgba(255,255,255,.05);}',
    },
    "Pale orange": {
        TYPE: 1,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#ffe49c', BG_LOW_PRIMARY: '#ffe49c',
        BG_SECONDARY: '#ffffff', BG_LOW_SECONDARY: '#ffffff',
        TC_PRIMARY: '#212121', TC_LOW_PRIMARY: '#424242',
        TC_SECONDARY: '#212121', TC_LOW_SECONDARY: '#424242', TC_LOW_SECONDARY_GLOBAL: '#000000',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(0,0,0,.05)', BG_SECONDARY_HOVER: 'rgba(0,0,0,.05)',
        PRIMARY_BORDER: 'rgba(0,0,0,.1)', SECONDARY_BORDER: 'rgba(0,0,0,.1)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #ffe49c; --bg-lowPrimary: #ffe49c; --bg-secondary: #ffffff; --bg-lowSecondary: #ffffff; --tc-primary: #212121; --tc-lowPrimary: #424242; --tc-secondary: #212121; --tc-lowSecondary: #424242; --tc-lowSecondaryGlobal: #000000; --link-color: #337ab7; --bg-primary-hover: rgba(0,0,0,.05); --bg-secondary-hover: rgba(0,0,0,.05); --primary-border: rgba(0,0,0,.1); --secondary-border: rgba(0,0,0,.1);}',
    },
    "Pale blue": {
        TYPE: 1,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#d8ecff', BG_LOW_PRIMARY: '#d8ecff',
        BG_SECONDARY: '#ffffff', BG_LOW_SECONDARY: '#ffffff',
        TC_PRIMARY: '#212121', TC_LOW_PRIMARY: '#424242',
        TC_SECONDARY: '#212121', TC_LOW_SECONDARY: '#424242', TC_LOW_SECONDARY_GLOBAL: '#000000',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(0,0,0,.05)', BG_SECONDARY_HOVER: 'rgba(0,0,0,.05)',
        PRIMARY_BORDER: 'rgba(0,0,0,.1)', SECONDARY_BORDER: 'rgba(0,0,0,.1)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #d8ecff; --bg-lowPrimary: #d8ecff; --bg-secondary: #ffffff; --bg-lowSecondary: #ffffff; --tc-primary: #212121; --tc-lowPrimary: #424242; --tc-secondary: #212121; --tc-lowSecondary: #424242; --tc-lowSecondaryGlobal: #000000; --link-color: #337ab7; --bg-primary-hover: rgba(0,0,0,.05); --bg-secondary-hover: rgba(0,0,0,.05); --primary-border: rgba(0,0,0,.1); --secondary-border: rgba(0,0,0,.1);}',
    },
    "Onyx": {
        TYPE: 0,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#0f0f0f', BG_LOW_PRIMARY: '#131313',
        BG_SECONDARY: '#0f0f0f', BG_LOW_SECONDARY: '#0f0f0f',
        TC_PRIMARY: '#ffffff', TC_LOW_PRIMARY: '#bbbbbb',
        TC_SECONDARY: '#ffffff', TC_LOW_SECONDARY: '#bbbbbb', TC_LOW_SECONDARY_GLOBAL: '#d5d5d5',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(255,255,255,.05)', BG_SECONDARY_HOVER: 'rgba(255,255,255,.05)',
        PRIMARY_BORDER: 'rgba(255,255,255,.05)', SECONDARY_BORDER: 'rgba(255,255,255,.05)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #0f0f0f; --bg-lowPrimary: #0f0f0f; --bg-secondary: #0f0f0f; --bg-lowSecondary: #0f0f0f; --tc-primary: #ffffff; --tc-lowPrimary: #bbbbbb; --tc-secondary: #ffffff; --tc-lowSecondary: #bbbbbb; --tc-lowSecondaryGlobal: #d5d5d5; --link-color: #337ab7; --bg-primary-hover: rgba(255,255,255,.05); --bg-secondary-hover: rgba(255,255,255,.05); --primary-border: rgba(255,255,255,.05); --secondary-border: rgba(255,255,255,.05);}',
    },
    "AMOLED": {
        TYPE: 0,
        FS_SM: '.8rem', FS_MD: '1.0rem', FS_LG: '1.5rem', FS_XL: '2rem',
        BG_PRIMARY: '#000000', BG_LOW_PRIMARY: '#050505',
        BG_SECONDARY: '#000000', BG_LOW_SECONDARY: '#000000',
        TC_PRIMARY: '#eeeeee', TC_LOW_PRIMARY: '#aaaaaa',
        TC_SECONDARY: '#eeeeee', TC_LOW_SECONDARY: '#aaaaaa', TC_LOW_SECONDARY_GLOBAL: '#d5d5d5',
        LINK_COLOR: '#337ab7',
        BG_PRIMARY_HOVER: 'rgba(255,255,255,.05)', BG_SECONDARY_HOVER: 'rgba(255,255,255,.05)',
        PRIMARY_BORDER: 'rgba(255,255,255,.05)', SECONDARY_BORDER: 'rgba(255,255,255,.05)',
        STR_THEME: '{--fs-sm: .8rem; --fs-md: 1.0rem; --fs-lg: 1.5rem; --fs-xl: 2rem; --bg-primary: #000000; --bg-lowPrimary: #050505; --bg-secondary: #000000; --bg-lowSecondary: #000000; --tc-primary: #eeeeee; --tc-lowPrimary: #aaaaaa; --tc-secondary: #eeeeee; --tc-lowSecondary: #aaaaaa; --tc-lowSecondaryGlobal: #d5d5d5; --link-color: #337ab7; --bg-primary-hover: rgba(255,255,255,.05); --bg-secondary-hover: rgba(255,255,255,.05); --primary-border: rgba(255,255,255,.05); --secondary-border: rgba(255,255,255,.05);}',
    },
};
var currentTheme = THEME["Default"];

function loadThemeList() {
    for(name in Object.keys(THEME)) {
        var option = document.createElement('option');
        option.classList.add('theme-select-options');
        option.innerHTML = Object.keys(THEME)[name];
        document.getElementById('themeSelector').appendChild(option);
    }
};
function setStrTheme(str, THEME) {
    var newFront = str.substring( 0, str.indexOf(":root") + ":root".length);
    var remain = str.substring(str.indexOf(":root") + ":root".length, str.length);
    var newEnd = remain.substring(remain.indexOf('}') + 1, remain.length);

    return newFront + THEME + newEnd;
};
function setTheme(THEME) {
    currentTheme = THEME;
    let root = document.documentElement;
    root.style.setProperty('--fs-sm', THEME.FS_SM);
    root.style.setProperty('--fs-md', THEME.FS_MD);
    root.style.setProperty('--fs-lg', THEME.FS_LG);
    root.style.setProperty('--fs-xl', THEME.FS_XL);
    root.style.setProperty('--bg-primary', THEME.BG_PRIMARY);
    root.style.setProperty('--bg-lowPrimary', THEME.BG_LOW_PRIMARY);
    root.style.setProperty('--bg-secondary', THEME.BG_SECONDARY);
    root.style.setProperty('--bg-lowSecondary', THEME.BG_LOW_SECONDARY);
    root.style.setProperty('--tc-primary', THEME.TC_PRIMARY);
    root.style.setProperty('--tc-lowPrimary', THEME.TC_LOW_PRIMARY);
    root.style.setProperty('--tc-secondary', THEME.TC_SECONDARY);
    root.style.setProperty('--tc-lowSecondary', THEME.TC_LOW_SECONDARY);
    root.style.setProperty('--tc-lowSecondaryGlobal', THEME.TC_LOW_SECONDARY_GLOBAL);
    root.style.setProperty('--link-color', THEME.LINK_COLOR);
    root.style.setProperty('--bg-primary-hover', THEME.BG_PRIMARY_HOVER);
    root.style.setProperty('--bg-secondary-hover', THEME.BG_SECONDARY_HOVER);
    root.style.setProperty('--primary-border', THEME.PRIMARY_BORDER);
    root.style.setProperty('--secondary-border', THEME.SECONDARY_BORDER);
    
    /*-----chrome address bar color-------*/
    $('meta[name=theme-color]').attr('content', THEME.BG_PRIMARY);

    /*-----Send theme to iframes-------*/
    var welcomeIF = document.getElementById("page-welcome").contentWindow;
    welcomeIF.postMessage(THEME, "*");
    diffstart = setStrTheme(diffstart, THEME.STR_THEME);
    newstart = setStrTheme(newstart, THEME.STR_THEME);
    starterror = setStrTheme(starterror, THEME.STR_THEME);
    if(document.getElementById("page").srcdoc != "") {
        document.getElementById("page").srcdoc = setStrTheme(document.getElementById("page").srcdoc, THEME.STR_THEME);
    }
};
function changeTheme(select) {
    setTheme(THEME[Object.keys(THEME)[select]]);
};

var drawerWidth = 1;
function openDrawer() {
    document.getElementById('edits-count').style.display = 'none';
	switch(drawerWidth) {
		case 0:
	        document.documentElement.style.setProperty('--m-left-panel-width', '48px');
	        drawerWidth = 1;
	        document.getElementById('next-diff').style.display = 'none';
			break;
		case 1:
	        document.documentElement.style.setProperty('--m-left-panel-width', '90px');
	        drawerWidth = 2;
	        document.getElementById('next-diff').style.display = 'none';
			break;
		case 2:
	        document.documentElement.style.setProperty('--m-left-panel-width', '0px');
	        drawerWidth = 0;
	        document.getElementById('next-diff').style.display = 'flex';
            document.getElementById('edits-count').style.display = 'block';
			break;
	}
}


/*#########################
-------- Left panel -----
#########################*/

document.getElementById('btn-logs').onclick = function() {
    togglePopup('logs');
}

document.getElementById('btn-settings').onclick = function() {
    closeSettingsSend();
    document.getElementById('max-days').value = regdays;
    document.getElementById('max-edits').value = countedits;
    if (countqueue == 0)
        document.getElementById('max-queue').value = "";
    else
        document.getElementById('max-queue').value = countqueue;
    togglePopup('settings');
    document.getElementById('btn-settings').blur();
};

function createChipCross(minus) {
    var chipCross = document.createElement('span');
    chipCross.textContent = '×';
    chipCross.addEventListener('click', function() {
        document.getElementById(minus).onclick((this.nextSibling).textContent, true);
    });
    return chipCross;
}
document.getElementById("btn-wl-u-add").onclick = function() {
    if (document.getElementById("wladdu").value !== "") {
        if (document.getElementById("wladdu").value.indexOf(",") == -1) {
            wlistu.push(document.getElementById("wladdu").value);
            var ul = document.getElementById("wlareau");
            var li = document.createElement('li');
            li.appendChild(createChipCross('btn-wl-u-delete'));
            li.appendChild(document.createTextNode(document.getElementById("wladdu").value));
            ul.appendChild(li);
	    document.getElementById("wladdu").value = "";
            $.ajax({ url: 'php/settings.php', type: 'POST', crossDomain: true, data:{action: 'set', query: "whitelist", wlusers: wlistu.join(',')}, dataType: 'json'});
        } else
            alert("Parameter is incorrect");
    }
};

<?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
document.getElementById("btn-bl-p-add").onclick = function() {
    if (document.getElementById("bl-p").value !== "") {
        if (document.getElementById("bl-p").value.indexOf(",") == -1) {
            customlist.push(document.getElementById("bl-p").value);
            var ulp = document.getElementById("blareap");
            var lip = document.createElement("li");
            lip.appendChild(createChipCross("btn-bl-p-delete"));
            lip.appendChild(document.createTextNode(document.getElementById("bl-p").value));
            ulp.appendChild(lip);
	    document.getElementById("bl-p").value = "";
            $.ajax({ url: "php/settings.php", type: "POST", crossDomain: true, data:{action: "set", query: "blacklist", blprojects: customlist.join(",")}, dataType: "json"});
        } else
            alert("Parameter is incorrect");
    }
};
'; } ?>

document.getElementById("btn-wl-p-add").onclick = function() {
    if (document.getElementById("wladdp").value !== "") {
        if (document.getElementById("wladdp").value.indexOf(",") == -1) {
            wlistp.push(document.getElementById("wladdp").value);
            var ul = document.getElementById("wlareap");
            var li = document.createElement('li');
            li.appendChild(createChipCross('btn-wl-p-delete'));
            li.appendChild(document.createTextNode(document.getElementById("wladdp").value));
            ul.appendChild(li);
	    document.getElementById("wladdp").value = "";
            $.ajax({ url: 'php/settings.php', type: 'POST', crossDomain: true, data:{action: 'set', query: "whitelist", wlprojects: wlistp.join(',')}, dataType: 'json'});
        } else
            alert("Parameter is incorrect");
    }
};

document.getElementById("btn-wl-u-delete").onclick = function(value, crossClick) {
    if (document.getElementById("wladdu").value !== "" || crossClick == true) {
        if (crossClick == true) var chipVal = value;
        else var chipVal = document.getElementById("wladdu").value;
        var index = wlistu.indexOf(chipVal);
        if (index !== -1)
            wlistu.splice(index, 1);
        $('ul#wlareau li:contains('+ chipVal +')').first().remove();
	    document.getElementById("wladdu").value = "";
        $.ajax({ url: 'php/settings.php', type: 'POST', crossDomain: true, data:{action: 'set', query: "whitelist", wlusers: wlistu.join(',')}, dataType: 'json'});
    }
};

<?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
document.getElementById("btn-bl-p-delete").onclick = function(value, crossClick) {
    if (document.getElementById("bl-p").value !== "" || crossClick == true) {
        if (crossClick == true) var chipVal = value;
        else var chipVal = document.getElementById("bl-p").value;
        var index = customlist.indexOf(chipVal);
        if (index !== -1)
            customlist.splice(index, 1);
        $("ul#blareap li:contains("+ chipVal +")").first().remove();
        document.getElementById("bl-p").value = "";
        $.ajax({ url: "php/settings.php", type: "POST", crossDomain: true, data:{action: "set", query: "blacklist", blprojects: customlist.join(",")}, dataType: "json"});
    }
};
'; } ?>

document.getElementById("btn-wl-p-delete").onclick = function(value, crossClick) {
    if (document.getElementById("wladdp").value !== "" || crossClick == true) {
        if (crossClick == true) var chipVal = value;
        else var chipVal = document.getElementById("wladdp").value;
        var index = wlistp.indexOf(chipVal);
        if (index !== -1)
            wlistp.splice(index, 1);	
        $('ul#wlareap li:contains('+ chipVal +')').first().remove();
        document.getElementById("wladdp").value = "";
        $.ajax({ url: 'php/settings.php', type: 'POST', crossDomain: true, data:{action: 'set', query: "whitelist", wlprojects: wlistp.join(',')}, dataType: 'json'});
    }
};

document.getElementById("btn-talk").onclick = function() {
	togglePopup('talkForm');
    scrollToBottom("talk-content");
    if (document.getElementById('badge-talk').style.background !== "rgb(251, 47, 47)")
        document.getElementById('badge-talk').style.background = "none";
    document.getElementById('btn-talk').blur();
};

function scrollToBottom(id){
   var element = document.getElementById(id);
   element.scrollTop = element.scrollHeight - element.clientHeight;
};

/*#########################
-------- popup-module -----
#########################*/

function queueClick() {
    if (document.getElementById('settings').style.display == "block")
        closeSettingsSend();
    closeAllPopups();
}

function closeAllPopups() {
    var popups = document.getElementsByClassName('popup-base');
    for(let i = 0; i < popups.length; i++) popups[i].style.display = 'none';
}

function togglePopup(popup) {
    if(document.getElementById(popup).style.display == 'none') {
        closeAllPopups();
        document.getElementById(popup).style.display = 'block';
    } else {
        document.getElementById(popup).style.display = 'none';
    }
};

function togglePopupDrawer(drawer, width, notClose) {
    if(document.getElementById(drawer).style.right == width && notClose) {
        document.getElementById(drawer).style.right = '0px';
        document.getElementById(drawer).style.boxShadow = '-8px 0 48px 0 rgba(0,0,0,.2)';
        document.getElementById(drawer + '-shadow').style.display = 'block';
        document.getElementById(drawer + '-shadow').style.backgroundColor = 'rgba(0,0,0,.05)';
    } else {
        document.getElementById(drawer).style.right = width;
        document.getElementById(drawer).style.boxShadow = '-8px 0 48px 0 rgba(0,0,0,0)';
        document.getElementById(drawer + '-shadow').style.display = 'none';
        document.getElementById(drawer + '-shadow').style.backgroundColor = 'rgba(0,0,0,0)';
    }
};

/*#########################
-------- i-btn-module -----
#########################*/

function toggleIBtn(btn, notOn) {
	if(document.getElementById(btn).style.paddingLeft == '22.5px' && notOn) {
		document.getElementById(btn).style.paddingLeft = '2.5px';
		document.getElementById(btn).style.backgroundColor = 'var(--bg-secondary-hover)';
	} else {
		document.getElementById(btn).style.paddingLeft = '22.5px';
		document.getElementById(btn).style.backgroundColor = '#24a464';
	}
}

/*#########################
--------- source code-----
#########################*/

document.getElementById('editBtn').onclick = function() {
	togglePopup('editForm');
}

document.getElementById('editForm-close-btn').onclick = function() {
	togglePopup('editForm');
}

function closeEditForm() {
	togglePopup('editForm');
}

document.getElementById('editForm-drawer-btn').onclick = function() {
    togglePopupDrawer('editForm-drawer', '-50%', true);
}

document.getElementById('editForm-drawer-shadow').onclick = function() {
    togglePopupDrawer('editForm-drawer', '-50%', false);
}

/*#########################
--------- settings -------
#########################*/

document.getElementById("btn-unlogin").onclick = function() {
    document.getElementById('btn-unlogin').blur();
    if (confirm("Do you confirm Logout?")) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', "php/oauth.php?action=unlogin", false);
        xhr.send();
        if ( xhr.responseText == "Unlogin is done")
            window.open("https://tools.wmflabs.org/swviewer/", "_self");
    }
};

document.getElementById('settings-close-btn').onclick = function() {
    closeSettingsSend();
    togglePopup('settings');
};

function closeSettingsSend() {
    if (document.getElementById("settings").style.display == "block") {
        if ((typeof document.getElementById('max-days').value !== "undefined") &&
            document.getElementById('max-days').value !== null &&
            document.getElementById('max-days').value !== "0" &&
            document.getElementById('max-days').value.match(/^\d+$/))
                regdays = parseInt(document.getElementById('max-days').value);
        if ((typeof document.getElementById('max-edits').value !== "undefined") &&
            document.getElementById('max-edits').value !== null &&
            document.getElementById('max-edits').value !== "0" &&
            document.getElementById('max-edits').value.match(/^\d+$/))
                countedits = parseInt(document.getElementById('max-edits').value);
        if ( (typeof document.getElementById('max-queue').value == "undefined") ||
            document.getElementById('max-queue').value == null ||
            document.getElementById('max-queue').value == "")
                countqueue = 0;
        if (document.getElementById('max-queue').value.match(/^\d+$/)) {
            countqueue = parseInt(document.getElementById('max-queue').value);
            if (countqueue !== 0)
                angular.element(document.getElementById("angularapp")).scope().removeLast();
        }

        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'numbers',
            editscount: countedits,
            regdays: regdays,
            countqueue: countqueue 
        }, dataType: 'json'});
    }
};

document.getElementById('settings-drawer-btn').onclick = function() {
    togglePopupDrawer('settings-drawer', '-50%', true);
};

document.getElementById('settings-drawer-shadow').onclick = function() {
    togglePopupDrawer('settings-drawer', '-50%', false);
}

document.getElementById('themeSelector').onchange = function() {
    changeTheme(document.getElementById('themeSelector').selectedIndex);

    $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
        action: 'set',
        query: 'theme',
        limit: Object.keys(THEME),
        theme: document.getElementById("themeSelector").selectedIndex
    }, dataType: 'json'});
    
    userColor.clear();
    downloadHistoryTalk();
};

document.getElementById('bottom-up-btn').onclick = function() {
	toggleIBtn('bottom-up-btn', true);
        var sendDirection;
        if (this.style.paddingLeft == '22.5px') {
            document.getElementById("queue").setAttribute("style", "display:flex; flex-direction:column-reverse");
            sendDirection = 1;
        }
        else {
            document.getElementById("queue").removeAttribute("style");
            sendDirection = 0;
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'direction',
            direction: sendDirection
        }, dataType: 'json'});
};

document.getElementById('sound-btn').onclick = function() {
	toggleIBtn('sound-btn', true);
        var sendSound;
        if (this.style.paddingLeft == '22.5px') {
            isSound = true;
            sendSound = 1;
        }
        else {
            isSound = false;
            sendSound = 0;
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'sound',
            sound: sendSound
        }, dataType: 'json'});
};

document.getElementById('registered-btn').onclick = function() {
	toggleIBtn('registered-btn', true);
        var sqlreg = 0;
        if (this.style.paddingLeft == '22.5px')
            sqlreg = 1;
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'registered',
            registered: sqlreg
        }, dataType: 'json'});
};

document.getElementById('new-pages-btn').onclick = function() {
	toggleIBtn('new-pages-btn', true);
        var sqlnew = 0;
        if (this.style.paddingLeft == '22.5px')
            sqlnew = 1;
        else {
            if (document.getElementById('onlynew-pages-btn').style.paddingLeft == '22.5px')
                document.getElementById('onlynew-pages-btn').click();
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'newbies',
            sqlnew: sqlnew
        }, dataType: 'json'});
};

document.getElementById('onlynew-pages-btn').onclick = function() {
	toggleIBtn('onlynew-pages-btn', true);
        var onlynew = 0;
        if (this.style.paddingLeft == '22.5px') {
            onlynew = 1;
            if (document.getElementById('new-pages-btn').style.paddingLeft !== '22.5px')
                document.getElementById('new-pages-btn').click();
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'onlynew',
            onlynew: onlynew
        }, dataType: 'json'});
};

<?php if ($isGlobal === true || $isGlobalModeAccess === true) { echo "
document.getElementById('small-wikis-btn').onclick = function() {
	toggleIBtn('small-wikis-btn', true);
        var sqlswmt = 0;
        if (this.style.paddingLeft == '22.5px') {
            sqlswmt = 1;
            if (isGlobalModeAccess === true)
                sqlswmt = 2;
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'swmt',
            swmt: sqlswmt
        }, dataType: 'json'});
};

document.getElementById('lt-300-btn').onclick = function() {
	toggleIBtn('lt-300-btn', true);
        var sqlusers = 0;
        if (this.style.paddingLeft == '22.5px') {
            sqlusers = 1;
            if (isGlobalModeAccess === true)
                sqlusers = 2;
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'users',
            users: sqlusers
        }, dataType: 'json'});
};
"; } ?>

function playSound (ps, ignoreIsSound) {
    if (isSound == true || ignoreIsSound === true) {
        audiopromise = ps.play();
        if (audiopromise !== undefined) {
            audiopromise.then( function() { return null; }).catch( function() { return null; });
        }
    }
};

document.getElementById("btn-add-ns").onclick = function () {
    if (document.getElementById("ns-input").value == null || document.getElementById("ns-input").value == "" || typeof document.getElementById("ns-input").value == "undefined")
        return;
    nsChange(document.getElementById("ns-input").value, "add");
}

document.getElementById("btn-delete-ns").onclick = function (value, crossClick) {
    if ((document.getElementById("ns-input").value == null || document.getElementById("ns-input").value == "" || typeof document.getElementById("ns-input").value == "undefined") && crossClick !== true)
        return;
    if (crossClick == true)
        var chipVal = value;
    else
        var chipVal = document.getElementById("ns-input").value;
    nsChange(chipVal, "delete");
}

function nsChange(val, action) {
    var checkChange = false;
    if (isNaN(val)) {
        var match = /^Other\s\((\d+)\)$/g.exec(val);
        if (match && typeof match[1] !== "undefined") {
            if (nsList2.indexOf(match[1]) !== -1) {
                nsList2.splice(nsList2.indexOf(match[1]), 1);
                checkChange = val;
            }
        }
        else {
           var checkNsVal = findKey(val.toLowerCase(), nsList);
           if (checkNsVal !== false) {
               if (action == "add")
                   if (nsList2.indexOf(checkNsVal) == -1) {
                       nsList2.push(checkNsVal);
                       checkChange = val;
                   }
               if (action == "delete") {
                   if (nsList2.indexOf(checkNsVal) !== -1) {
                       nsList2.splice(nsList2.indexOf(checkNsVal), 1);
                       checkChange = val;
                   }
               }
            }
        }
    }
    else {
        if (action == "add")
            if (nsList2.indexOf(val) == -1) {
                nsList2.push(val);
                if (typeof nsList[val] !== "undefined")
                    checkChange = nsList[val];
                else
                    checkChange = "Other (" + val + ")";
            }
        if (action == "delete")
            if (nsList2.indexOf(val) !== -1) {
                nsList2.splice(nsList2.indexOf(val), 1);
                if (typeof nsList[val] !== "undefined")
                    checkChange = nsList[val];
                else
                    checkChange = "Other (" + val + ")";
            }
    }
    
    if (checkChange !== false) {
        if (action == "add") {
            var ul = document.getElementById("nsList");
            var li = document.createElement('li');
            li.appendChild(createChipCross('btn-delete-ns'));
            li.appendChild(document.createTextNode(checkChange));
            ul.appendChild(li);
        }
        else {
            $('ul#nsList li:contains('+ checkChange +')').first().remove();
        }
            
        $.ajax({ url: 'php/settings.php', type: 'POST', crossDomain: true, data:{action: 'set', query: "namespaces", ns: nsList2.join(',')}, dataType: 'json'});
    }

    document.getElementById("ns-input").value = "";
}

function findKey(val, arr) {
    for (var key in arr) {
        if (arr[key].toLowerCase() == val)
            return key;
    }
    return false;
}

function addSandbox(sbList, wiki, page) {
    if (sbList.hasOwnProperty(wiki))
        sbList[wiki] = sbList[wiki] + ", " + page;
};

/*#########################
--------- talk -------
#########################*/

document.getElementById('talk-close-btn').onclick = function() {
    togglePopup('talkForm');
};

document.getElementById('talk-drawer-btn').onclick = function() {
    togglePopupDrawer('talk-drawer', '-50%', true);
};

document.getElementById('talk-drawer-shadow').onclick = function() {
    togglePopupDrawer('talk-drawer', '-50%', false);
}

var userColor = new Map();
function genRandColor() {
    if (currentTheme.TYPE) {
        return `hsl(${Math.floor(Math.random() * 361)}, ${(Math.floor(Math.random() * 35) + 30)}%, ${Math.floor(Math.random() * 25) + 25}%`;
    } else {
        return `hsl(${Math.floor(Math.random() * 361)}, ${(Math.floor(Math.random() * 60) + 40)}%, ${Math.floor(Math.random() * 25) + 50}%`;
    }
}
function getUserColor(user) {
    if(!userColor.has(user)) userColor.set(user, genRandColor(user));
    return userColor.get(user);
}

function parseDate(date) {
  const parsed = Date.parse(date);
  if (!isNaN(parsed)) {
    return parsed;
  }

  return Date.parse(date.replace(/-/g, '/').replace(/[a-z]+/gi, ' '));
}

addToTalk = function (timestamp, nickname, text) {
    var hours, minuts, seconds, now;
    if (timestamp == null) {
        now = new Date;

        hours = now.getUTCHours().toString();
        minuts = now.getUTCMinutes().toString();
        seconds = now.getUTCSeconds().toString();
    }
    else {
        now = new Date(parseDate(timestamp));

        hours = now.getHours().toString();
        minuts = now.getMinutes().toString();
        seconds = now.getSeconds().toString();
    }

        if (hours.length == "1") hours = "0" + hours;
        if (minuts.length == "1") minuts = "0" + minuts;
        if (seconds.length == "1") seconds = "0" + seconds;

        var textTime ='[' + hours + ':' + minuts + ':' + seconds + ']';
        var textUser = '<' + nickname + '>';
        var textMessage = text;

        var blockTime = document.createElement('div');
        blockTime.className = 'phrase-line1';
        var blockUser = document.createElement('div');
        blockUser.className = 'phrase-line2';
        var blockMessage = document.createElement('div');
        blockMessage.className = 'phrase-line3';

        blockTime.textContent = textTime;
        blockUser.textContent = textUser;

        /* Find and attach links in user message. */
        var linkPattern = /\b(http|https):\/\/\S+/g;
        if(linkPattern.test(textMessage)) {
            var links = textMessage.match(linkPattern);
            subMessStart= 0;
            subMessEnd = textMessage.indexOf(links[0]);
            for(let index in links) {
                var subMessage = document.createElement('span');
                subMessage.textContent = textMessage.substring(subMessStart, subMessEnd);
                blockMessage.appendChild(subMessage);
                
                var link = document.createElement('a');
                link.href = links[index];
                link.target = "_blank";
                link.style.wordBreak = "break-all";
                link.textContent = links[index];
                blockMessage.appendChild(link);
                
                subMessStart = (subMessEnd + links[index].length);
                subMessEnd = subMessStart + (textMessage.substring(subMessStart, textMessage.length)).search(linkPattern);
            }
            var subMessage = document.createElement('span');
            subMessage.textContent = textMessage.substring(subMessStart, textMessage.length);
            blockMessage.appendChild(subMessage);
        } else {
            blockMessage.textContent = textMessage;
        }

        var blockPhrase = document.createElement('div');
        blockPhrase.className = 'phrase-talk';
        blockPhrase.appendChild(blockTime);
        blockPhrase.appendChild(blockUser);
        blockPhrase.appendChild(blockMessage);

        blockPhrase.style.color = getUserColor(nickname);

        document.getElementById('form-talk').appendChild(blockPhrase);
        scrollToBottom("talk-content");
}

addToTalkSection = function(datatext) {
    var blockMessage = document.createElement('div');
    blockMessage.className ="days-ago-talk";
    blockMessage.textContent = datatext;

    var blockMessageLL = document.createElement('div');
    blockMessageLL.className = "days-ago-talk-LL"
    var blockMessageRL = document.createElement('div');
    blockMessageRL.className = "days-ago-talk-RL"

    var blockPhrase = document.createElement('div');
    blockPhrase.className = 'phrase-talk days-ago-talk-flex';

    blockPhrase.appendChild(blockMessageLL);
    blockPhrase.appendChild(blockMessage);
    blockPhrase.appendChild(blockMessageRL);

    document.getElementById('form-talk').appendChild(blockPhrase);
    scrollToBottom("talk-content");
}

var daysAgoToday = false;
function downloadHistoryTalk() {
    $("#form-talk").empty();
    xhr.open('Post', "php/talkHistory.php", false);
    var formData = new FormData();
    formData.append("action", "get");
    xhr.send(formData);
    if (xhr.responseText == "Invalid request")
        location.reload();
    var talkHistory  = JSON.parse(xhr.responseText);
    var options = {year: 'numeric', month: 'long', day: 'numeric', weekday: 'long', timezone: 'UTC'};

    historyCount = 0;
    for(i=4; i !== -1; i--) {
        var daysAgo = null;
        if (talkHistory.hasOwnProperty(i)) {
            if (talkHistory[i] !== null && talkHistory[i].length > 0) {
                if (i==0) {
                    daysAgo = "Today";
                    daysAgoToday = true;
                }
                else {
                    if (i==1)
                        daysAgo = "Yesterday";
                    else {
                        var dateHistory = new Date(Date.now() - (i*1000*60*60*24));
                        daysAgo = dateHistory.toLocaleString("en-US", options);
                    }
                }

                historyCount++;
                addToTalkSection(daysAgo);

                talkHistory[i].forEach(function(el) {
                    addToTalk(el['msgtime'], el['name'], el['text']);
                });
            }
        }
    }
}


var talkWidth = $('#talk-content').outerWidth();
$(window).resize(function() {
  if (talkWidth != $('#talk-content').outerWidth()) {
    talkWidth = $('#talk-content').outerWidth();
    scrollToBottom("talk-content");
  }
});

/*#########################
--------- Logs -------
#########################*/

var searchPhrase = "", action = "", logsLimit = 40, logsOffset = 0;

document.getElementById('logs-refresh-btn').onclick = function() {
    document.getElementById('actionSelector').selectedIndex = 0;
    action = "";
    logsOffset = 0;
    getLogs();
}
document.getElementById('logs-close-btn').onclick = function() {
    togglePopup('logs');
};
document.getElementById('nextLogs').onclick = function() {
    logsOffset += logsLimit;
    getLogs(searchPhrase);
}
document.getElementById('prevLogs').onclick = function() {
    logsOffset -= logsLimit;
    if(logsOffset < 0) logsOffset = 0;
    getLogs(searchPhrase);
}
document.getElementById('actionSelector').onchange = function() {
    action = this.value;
}
function handleLogsUI() {
    if(logsOffset == 0) {
        document.getElementById('prevLogs').style.display = 'none';
        document.getElementById('prevLogs').parentElement.style.justifyContent = 'flex-end';
    } else {
        document.getElementById('prevLogs').style.display = 'unset';
        document.getElementById('prevLogs').parentElement.style.justifyContent = 'space-between';
    }
    if(document.getElementById('logsTable').childElementCount <= logsLimit) {
        document.getElementById('nextLogs').style.display = "none";
        var noMore = document.createElement('div');
        noMore.style.padding = "8px 0";
        noMore.style.textAlign = "center";
        noMore.style.color = "darkgray";
        noMore.textContent = "No more Logs";
        document.getElementById('logsBox').append(noMore);
    } else {
        document.getElementById('nextLogs').style.display = "unset";
    }
}
function getLogs(sp) {
    if(arguments.length == 0) searchPhrase = "";
    else searchPhrase = sp;
    var logsReq = new XMLHttpRequest();
    logsReq.open("GET", "php/logs.php?sp=" + searchPhrase + "&st=" + action + "&li=" + logsLimit + "&of=" + logsOffset, true);
    logsReq.onreadystatechange = function () {
        if (logsReq.readyState === 4 && logsReq.status === 200) {
            document.getElementById('logsBox').innerHTML = logsReq.responseText;
            document.getElementById('logsBox').parentElement.scrollTop = 0;

            handleLogsUI();
        }
    }

    logsReq.send();
}

document.getElementById('btn-searchLogs').onclick = function() {
    var searchInput = document.getElementById('search-input');
    logsOffset = 0;
    getLogs(searchInput.value);
}
getLogs();


/*#########################
--------- onLoad -------
#########################*/

window.onload = function() {
    loadThemeList();
    if (settingslist['theme'] !== null && typeof settingslist['theme'] !== "undefined" && settingslist['theme'] !== "" && ( settingslist['theme'] >= 0 && settingslist['theme'] < (Object.keys(THEME)).length) ) {
        document.getElementById('themeSelector').selectedIndex = settingslist['theme'];
        changeTheme(parseInt(settingslist['theme']));
    } else changeTheme(0);
    downloadHistoryTalk();
    document.getElementById('loading').style.display = "none";
    document.getElementById('app').style.display = "block";
};

</script>
<script src="js/swv.js"></script>
<script>uiDisableList();</script>
</body>
</html>
