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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
        <!-- AngularJS, jQuery, Moment, pwacompat -->
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/angular.js/1.7.2/angular.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/angular-ui/0.4.0/angular-ui.min.js"></script>
        <!-- <script type="text/javascript" async src="//cdn.jsdelivr.net/npm/pwacompat@2.0.9/pwacompat.min.js"></script> -->
        <script async src="js/pwacompat.js"></script>

        <!-- Fonts, stylesheet-->
        <link href='//tools-static.wmflabs.org/fontcdn/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/variables.css">
        <link rel="stylesheet" href="css/base.css">
        <link rel="stylesheet" href="css/modules.css">
        <link rel="stylesheet" href="css/swv-raw.css?v=1.2">
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

        <div class='login-base secondary-cont'>
            <div class='login-card'>
                <div>
                    <span class='fs-xl' style='font-weight: bold;'>Welcome!</span>
                    <a id='abtn' class='i-btn__accent accent-hover' style='margin: 16px 0; color: var(--tc-accent) !important; padding: 0 24px; text-decoration: none !important;' href='https://tools.wmflabs.org/swviewer/php/oauth.php?action=start'>OAuth Login</a>
                    <span class='fs-xs'>To use this application <a rel='noopener noreferrer' target='_blank' href='https://en.wikipedia.org/wiki/Wikipedia:Rollback'>local</a> or <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Global_rollback'>global</a> rollback is required.</span>
                </div>
                <div>
                    <span class='i-btn__secondary-outlined secondary-hover fs-md' style='height: 35px; margin-bottom: 8px;' onclick='openPO();'>About</span>
                    <span class='fs-xs'>Brought to you by <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/User:Iluvatar'>Iluvatar</a>, <a rel='noopener noreferrer' target='_blank' href='https://ajbura.github.io'>ajbura</a>, <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/User:1997kB'>1997kB</a></span>
                </div>    
            </div>
        </div>
        
        <!-- Global requests | Popup-overlay -->
        <div id='about' class='po__base'>
            <div class='po__header action-header'>
                <span class='action-header__title fs-lg'>About</span>
                <div class='mobile-only secondary-hover' title='Close [esc]' onclick='closePO()'>
                    <img class='touch-ic secondary-icon' src='./img/cross-filled.svg' alt='Cross image'>
                </div>
                <span class='desktop-only po__esc secondary-hover fs-md' onclick='closePO()'>esc</span>
            </div>
            <div class='po__content'>
                <div class='po__content-body secondary-scroll'>
                    <div class='fs-md'>
                        SWViewer: see <a href='https://meta.wikimedia.org/wiki/SWViewer' rel='noopener noreferrer' target='_blank'>documentation page</a>.<br><br>

                        Any questions, please <a href='https://meta.wikimedia.org/wiki/Talk:SWViewer' rel='noopener noreferrer' target='_blank'>ask here</a>.<br><br>
    
                        Caution! Big internet traffic. Internet Explorer/Microsoft Edge is not supported.<br><br>

                        Privacy: Application saves only name of your account, your internal app settings, your actions via app (see 'log'), count of logins to app, count of app opens, date of last login to app and contents of the chat.<br><br>
    
                        Licensing:<br>
                        Copyright &#169; mainteiners of SWViewer, 2017-2019<br><br>
    
                        Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:<br><br>
    
                        The above copyright notice and this permission notice shall be included in all
                        copies or substantial portions of the Software.<br><br>
    
                        THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.<br><br>
    
                        bump.mp3: <a rel='noopener noreferrer' target='_blank' href='https://freesound.org/people/Porphyr/sounds/208435/'>license and author of original file</a><br>
                        message.mp3: <a rel='noopener noreferrer' target='_blank' href='https://freesound.org/people/elmasmalo1/sounds/377017/'>license and author of original file</a><br>
                        privateMessage.mp3: <a rel='noopener noreferrer' target='_blank' href='https://freesound.org/people/rhodesmas/sounds/342749/'>license and author of original file</a><br>
                        Auth and some API-requests are based on a <a rel='noopener noreferrer' target='_blank' href='https://tools.wmflabs.org/oauth-hello-world/index.php?action=download'>that code</a><br>
                    </div>
                </div>
            </div>
        </div>
        <!-- po Overlay-->
        <div id='POOverlay' class='po__overlay' onclick='closePO()'></div>

        <script>
            var lastOpenedPO = undefined;
            function openPO (po = 'about') {
                document.getElementById(po).classList.add('po__active');
                document.getElementById('POOverlay').classList.add('po__overlay__active');
                lastOpenedPO = po;
            }
            function closePO () {
                if (lastOpenedPO !== undefined) {
                    document.getElementById(lastOpenedPO).classList.remove('po__active');
                    document.getElementById('POOverlay').classList.remove('po__overlay__active');
                }
            }
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

<!-- Loading UI -->
<div id="loading" class="secodnary-cont">
    <div class="loading-icon">
        <img src="./img/swviewer-logo-raw.svg">
    </div>
    <span class="loading-text fs-xl">SWViewer</span>
</div>

<!-- Application UI -->
<div id="angularapp" ng-app="swv" ng-controller="Queue">
    <div class="base-container" id="app">
        <div id="baseGrid" class="base-grid">
            <!-- sidebar -->
            <div id="sidebar" class="sidebar-base primary-cont">
                <div class="sidebar__options">
                    <div id="btn-home" class="tab__active primary-hover" title="SWViewer [esc]" onclick="closePW()">
                        <img class="touch-ic primary-icon" src="./img/swviewer-filled.svg" alt="SWViewer image">
                    </div>
                    <div id="btn-talk" class="primary-hover" title="Talk [t]" onclick="openPW('talkForm')">
                        <span class="badge-ic badge-ic__primary" style="background: none; color: var(--bc-primary);" id="badge-talk">{{filteredUsersTalk.length}}</span>
                    </div>  
                    <div id="btn-logs" class="primary-hover" title="Logs [l]" onclick="openPW('logs')">
                        <img class="touch-ic primary-icon" src="./img/doc-filled.svg" alt="Logs image">
                    </div>
                    <div id="btn-unlogin" class="primary-hover" title="Sign out [u]">
                        <img class="touch-ic primary-icon" src="./img/power-filled.svg" alt="Logout image">
                    </div>
                    <div id="btn-about" class="primary-hover" title="About" onclick="openPO('about')">
                        <img class="touch-ic primary-icon" src="./img/about-filled.svg" alt="About image">
                    </div>
                    <div id="btn-settings" class="primary-hover" title="Settings and quick links [s]" onclick="openPW('settings')">
                        <img class="touch-ic primary-icon" src="./img/settings-filled.svg" alt="Settings image">
                    </div>
                </div>
            </div>
            <!-- Drawer -->
            <div id="queueDrawer" class="drawer-base primary-cont">
                <div class="edit-queue-base">
                    <div class="action-header eq__header">
                        <span class="action-header__title fs-lg">Queue</span>
                    </div>
                    <div class="eq__body">
                        <div class="queue primary-scroll" id="queue">
                            <div id="edits-Queue" ng-repeat="edit in edits track by $index" onclick="queueClick()">
                                <div class="queue-row fs-sm primary-hover" ng-style="editColor(edit)" ng-click="select(edit)">{{edit.wiki}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Status Bar -->
            <div class="statusbar-base">

            </div>
            <!-- Main Window -->
            <div class="window-base secondary-cont">
                <div class="diff-container">
                    <!-- Mobile navbar -->
                    <div class="main-navbar action-header mobile-only">
                        <div class="primary-hover" onclick="openSidebar();">
                            <img class="touch-ic primary-icon" src="./img/drawer-filled.svg" alt="Navigation image">
                        </div>
                        <!-- <span class="action-header__title fs-xl">Home</span> -->
                        <div class="primary-hover" title="Talk [t]" onclick="openPW('talkForm')">
                            <span id="badge-talk-ex1" class="badge-ic badge-ic__primary" style="background: none; color: var(--bc-primary);">{{filteredUsersTalk.length}}</span>
                        </div>
                        <div class="primary-hover" title="Logs [l]" onclick="openPW('logs')">
                            <img class="touch-ic primary-icon" src="./img/doc-filled.svg" alt="Logs image">
                        </div>
                        <div class="desktop-only secondary-hover" onclick="togglMoreControl();">
                            <img class="touch-ic secondary-icon" src="./img/arrow-up-filled.svg" alt="More option img">
                        </div>
                        <div id="moreOptionBtnMobile" class="primary-hover disabled" onclick="togglMoreControl();">
                            <img class="touch-ic primary-icon" src="./img/v-dots-filled.svg" alt="More option img">
                        </div>
                        <div id="btn-drawer" class="primary-hover" style="position: relative;">
                            <span class="drawer-btn__edits-count">{{edits.length}}</span>
                            <img class="touch-ic primary-icon" src="./img/drawer-filled.svg" alt="Drawer image">
                        </div>
                    </div>
                    <!-- description container -->
                    <div id="description-container" class="description-container fs-md" style="display: none; margin-top: 0;">
                        <div class="desc-un">
                            <div id="us" class="fs-sm">User: <div id="userLinkSpec" ng-click="openLink('diff');"></div></div>
                            <div id="ns" class="fs-sm"></div>
                        </div>
                        <div class="desc-wt">
                            <div id="wiki" class="fs-sm"></div>
                            <div id="tit" class="fs-sm"></div>
                        </div>
                        <div class="desc-c">
                            <div id="com" class="fs-sm"></div>
                        </div>
                    </div>
                    <!-- Mobile next diff button -->
                    <div id="next-diff" class="next-diff accent-hover mobile-only" ng-click='nextDiff()'>
                        <div>
                            <img class="touch-ic accent-icon" src="./img/swviewer-filled.svg" alt="Next diffrence image">
                        </div>
                        <div id="next-diff-title" class="fs-md">Fetching</div>
                    </div>
                    <!-- Controls -->
                    <div id="moreControlOverlay" class="more-control__overlay"  onclick="togglMoreControl();"></div>
                    <div id="controlsBase" class="controls-base floatbar"  style="display: none;">
                        <!-- More control -->
                        <div id="moreControl" class="more-control more-control__hidden secondary-scroll">
                            <a class="seconary-hover fs-md" href='{{selected.server_url}}{{selected.script_path}}/index.php?title={{selected.title}}&action=history' rel='noopener noreferrer' target='_blank'>View history</a>
                            <a class="secondary-hover fs-md" href='https://tools.wmflabs.org/guc/?src=hr&by=date&user={{selected.user}}' rel='noopener noreferrer' target='_blank'>Global contribs</a>
                            <a class="secondary-hover fs-md" href='https://meta.wikimedia.org/wiki/Special:CentralAuth?target={{selected.user}}' id="CAUTH" rel='noopener noreferrer' target='_blank'>Central auth</a>
                            <span class="disabled secondary-hover fs-md" ng-click="requestsForm();" onclick="openPO('localRequests')">Local requests</span>
                            <span class="disabled secondary-hover fs-md" ng-click="requestsForm();" onclick="openPO('globalRequests')"> Global requests</span>
                        </div>
                        <!-- Control buttons -->
                        <div id="control" class="toolbar">
                            <div class="desktop-only secondary-hover" onclick="togglMoreControl();" title="More options">
                                <img class="touch-ic secondary-icon" src="./img/v-dots-filled.svg" alt="More option img">
                            </div>
                            <div id="browser" class="secondary-hover" ng-click="browser();" title="Open in browser window [o]">
                                <img class="touch-ic secondary-icon" src="./img/open-newtab-filled.svg" alt="Open in new tab img">
                            </div>
                            <div id="editBtn" class="secondary-hover" ng-click="checkEdit();" onclick="openPW('editForm')" title="Edit source [e]">
                                <img class="touch-ic secondary-icon" src="./img/edit-filled.svg" alt="Edit img">
                            </div>
                            <div id="customRevertBtn" class="secondary-hover" ng-click="customRevertSummary();" title="Rollback with summary [y]">
                                <img class="touch-ic secondary-icon" src="./img/custom-rollback-filled.svg" alt="Custom rollback img">
                            </div>
                            <div id="revert" class="secondary-hover" ng-click="Revert();" title="Quick Rollback [r]">
                                <img class="touch-ic secondary-icon" src="./img/rollback-filled.svg" alt="Rollback img">
                            </div>
                            <div id="back" class="secondary-hover" ng-click="Back();" title="Previous difference [Left square bracket or p]">
                                <img class="touch-ic secondary-icon" src="./img/arrow-left-filled.svg" alt="back image">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Welcome page -->
                    <div id='page-welcome' class='welcome-base frame-diff secondary-scroll' style='display: block;'>
                        <label class="fs-xl" style="font-weight: bold;">Welcome back!</label>
                        <div class="welcome-box">
                            <form class="stat-search__base" onsubmit="searchStat();">
                                <input id="statInput" class="i-input__secondary secondary-placeholder fs-md" type="text" autocomplete="off" placeholder="Search user">
                                <button class="i-btn__accent accent-hover fs-md" type="submit">Search</button>
                            </form>
                            <div id="statContainer" class="stat-container">
                                <div>
                                    <span id="rollbackSpan" class="fs-xl" style="color: #c8b40e;">0</span>
                                    <span class="fs-lg">Rollback</span>
                                </div>
                                <div>
                                    <span id="undoSpan" class="fs-xl" style="color: #db24b0;">0</span>
                                    <span class="fs-lg">Undo</span>
                                </div>
                                <div>
                                    <span id="deleteSpan" class="fs-xl" style="color: #672dd2;">0</span>
                                    <span class="fs-lg">Delete</span>
                                </div>
                                <div>
                                    <span id="editSpan" class="fs-xl" style="color: #2dd280;">0</span>
                                    <span class="fs-lg">Edit</span>
                                </div>
                                <div>
                                    <span id="warnSpan" class="fs-xl" style="color: #d92c26;">0</span>
                                    <span class="fs-lg">Warn</span>
                                </div>
                                <div>
                                    <span id="reportSpan" class="fs-xl" style="color: #e3791c;">0</span>
                                    <span class="fs-lg">Report</span>
                                </div>
                                <div>
                                    <span id="protectSpan" class="fs-xl" style="color: #1cb3e3">0</span>
                                    <span class="fs-lg">Protect</span>
                                </div>
                            </div>
                            
                            <div class="list-container">
                                <label class="fs-md">Reminder</label>
                                <ul class="i-ul fs-sm">
                                    <li class="i-ul__imp">IMPORTANT: If app is not working properly, please re-login and clear cache.</li>
                                    <li>Responsibility for edits rests with the owner of the account with which they are made.</li>
                                    <li class="i-ul__imp">Please DO NOT sends warns if you don’t know the language (except in case of pure vandalism).</li>
                                    <li>If you find a bug, please <a rel='noopener noreferrer' target="_blank" href="https://meta.wikimedia.org/wiki/Talk:SWViewer">report</a> it! Also attach screenshots or/and data from the browser console.</li>
                                </ul>
                            </div>
                            <div class="list-container">
                                <label class="fs-md">Tips</label>
                                <ul class="i-ul fs-sm">
                                    <li>If you are using Android and Chrome browser, then you can switch app to full-screen mode (without the address bar): Browser's settings => Add to Home screen and opens via label on screen.</li>
                                </ul>
                            </div>
                            <div class="list-container">
                                <label class="fs-md">What's new </label>
                                <ul class="i-ul fs-sm">
                                    <label class="i-ul__subheader fs-sm">21 October 2019</label>
                                    <li class="i-ul__imp">UI & UX improvments.</li>
                                    <li class="i-ul__imp">Global mode and undo for non-GRs.</li>
                                    <li class="i-ul__imp">New sounds, RH mode, Anonymous edits and Only new pages features added into settings.</li>
                                    <li class="i-ul__imp">Advanced menu for difference viewer.</li>
                                    <li class="i-ul__imp">Total statistics on welcome page.</li>
                                    <li class="i-ul__imp">Bug fixed!</li>
                                </ul>
                                <ul id="moreWN" class="i-ul fs-sm" style="overflow: hidden; height: 0px;">
                                    <label class="i-ul__subheader fs-sm">14 June 2019</label>
                                    <li>We now support themes, and a new logo again but this is last.. swear :P</li>
                                    <li>Also we now have customizable templates and edit summaries for each wikis, add for yours <a rel='noopener noreferrer' target="_blank" href="https://meta.wikimedia.org/wiki/SWViewer/config.json">here</a>.</li>
                                    <label class="i-ul__subheader fs-sm">24 March 2019</label>
                                    <li>Fully implimented new design along with new logo :)</li>
                                    <li>New design for desktop and mobile.</li>
                                    <li>Single login for all users (GR as well as local rollback).</li>
                                    <label class="i-ul__subheader fs-sm">Older</label>
                                    <li>50 new small projects have been added to the <a rel='noopener noreferrer' target="_blank" href="https://meta.wikimedia.org/wiki/SWViewer/wikis">SW list</a>.</li>
                                    <li>Added option to select an additional list of projects with less than 300 active users. Which includes about 30 projects, including the best of the bests sebwiki. Check app's settings.</li>
                                    <li>Added <a rel='noopener noreferrer' target="_blank" href="https://meta.wikimedia.org/wiki/SWViewer#Hotkeys">hotkeys</a>.</li>
                                    <li>Synchronization: If you doing rollback, edits of that page will be removed from queue of all users online in app.</li>
                                </ul>
                                <button class="i-btn__secondary-outlined secondary-hover fs-md" onclick="toggleMoreWN(this);">Show more</button>
                            </div>
                        </div>
                    </div>
                    <!-- Difference iframe -->
                    <div class="iframe-container frame-diff secondary-scroll" style="display: none;" onscroll="hideDesc(this)">
                        <iframe id='page' style="min-height: 100%;" title='Diff' sandbox='allow-same-origin allow-scripts' scrolling='no'></iframe>
                    </div>

                    <!-- talkForm | popup-window -->
                    <div id="talkForm" class="pw__base" style="display: none;">
                        <!--pw Header-->
                        <div class="pw__header action-header">
                            <div class="mobile-only secondary-hover" onclick="openSidebar();">
                                <img class="touch-ic secondary-icon" src="./img/drawer-filled.svg" alt="Box Image">
                            </div>
                            <span class="action-header__title fs-xl">Talk</span>
                            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePW()">
                                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
                            </div>
                            <div class="mobile-only secondary-hover" onclick="openPWDrawer('talkPWDrawer', 'talkPWOverlay')">
                                <img class="touch-ic touch-ic__w-free secondary-icon" src="./img/people-filled.svg" alt="People image">
                            </div>
                            <span class="desktop-only pw__esc secondary-hover fs-md" onclick="closePW()">esc</span>
                        </div>
                        <!--pw Content-->
                        <div class="pw__content">
                            <div id="talk-content" class="pw__content-body secondary-scroll">
                                <div id="form-talk"></div>
                            </div>

                            <div class="pw__floatbar">
                                <form onsubmit="document.getElementById('btn-send-talk').onclick();"><input  id="phrase-send-talk" class="secondary-placeholder fs-md" autocomplete="off" onfocus="scrollToBottom('talk-content')" title="Text to sent" max-length="600" placeholder="What's on your mind?"></form>
                                <div id="btn-send-talk" class="secondary-hover" title="Send">
                                    <img class="touch-ic secondary-icon" src="./img/send-filled.svg" alt="Send image">
                                </div>
                            </div>
                        </div>
                        <!--pw Drawer-->
                        <div id="talkPWDrawer" class="pw__drawer secondary-scroll">
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">People</span>
                            </div>
                            <div class="pw__drawer__content">
                                <div class="user-container fs-md" ng-repeat="talkUser in users|unique: talkUser as filteredUsersTalk">
                                    <div class="user-talk" onclick="selectTalkUsers(this)">{{talkUser}}</div>
                                    <a class="user-talk-CA" href="https://meta.wikimedia.org/wiki/Special:CentralAuth/{{talkUser}}" target="_blank">CA</a>
                                </div>
                                <div ng-repeat="talkUserOffline in offlineUsers track by $index">
                                    <div class="user-talk fs-md" style="color: gray;">{{talkUserOffline}}</div>
                                </div>
                            </div>
                        </div>
                        <!--pw Overlay-->
                        <div id="talkPWOverlay" class="pw__overlay" onclick="closePWDrawer('talkPWDrawer', 'talkPWOverlay')"></div>
                    </div>

                    <!-- Logs | popup-window -->
                    <div id="logs" class="pw__base" style="display: none; grid-template-areas: 'pw__header pw__header' 'pw__content pw__content';">
                        <!--pw Header-->
                        <div class="pw__header action-header">
                            <div class="mobile-only secondary-hover" onclick="openSidebar();">
                                <img class="touch-ic secondary-icon" src="./img/drawer-filled.svg" alt="Box Image">
                            </div>
                            <span class="action-header__title fs-xl">Logs</span>
                            <div class="secondary-hover" title="Refresh" onclick="refreshLogs()">
                                <img class="touch-ic secondary-icon" src="./img/reload-filled.svg" alt="Reload image">
                            </div>
                            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePW()">
                                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
                            </div>
                            <span class="desktop-only pw__esc secondary-hover fs-md" onclick="closePW()">esc</span>
                        </div>
                        <!--pw Content-->
                        <div class="pw__content">
                            <div class="pw__content-body secondary-scroll" style="padding: 0;">
                                <div id="logsBox"></div>
                                <div class="logBox-control">
                                    <button id="prevLogs" class='i-btn__secondary-outlined secondary-hover fs-md' style="display: none;">Previous</button>
                                    <button id="nextLogs" class='i-btn__secondary-outlined secondary-hover fs-md' style="display: none;">Next</button>
                                </div>
                            </div>

                            <div class="pw__floatbar">
                                <form onsubmit="searchLogs();"><input id="logsSearch-input"  class="secondary-placeholder fs-md" autocomplete="off" title="Search logs" max-length="600"" placeholder="Search for user or wiki."></form>
                                <div style="width: unset;">
                                    <select id="actionSelector" class="i-select__secondary fs-md">
                                        <option value="">All actions</option>
                                        <option value="rollback">Rollback</option>
                                        <option value="undo">Undo</option>
                                        <option value="delete">Delete</option>
                                        <option value="edit">Edit</option>
                                        <option value="warn">Warn</option>
                                        <option value="report">Report</option>
                                        <option value="protect">Protect</option>
                                    </select>
                                </div>
                                <div id="btn-searchLogs" class="secondary-hover" title="Search" onclick="searchLogs()">
                                    <img class="touch-ic secondary-icon" src="./img/search-filled.svg" alt="Search image">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Source | popup-window -->
                    <div id="editForm" class="pw__base" style="display: none;">
                        <!--pw Header-->
                        <div class="pw__header action-header">
                            <div class="mobile-only secondary-hover" onclick="openSidebar();">
                                <img class="touch-ic secondary-icon" src="./img/drawer-filled.svg" alt="Box Image">
                            </div>
                            <span class="action-header__title fs-xl">Edit Source</span>
                            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePW()">
                                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
                            </div>
                            <div class="mobile-only secondary-hover" onclick="openPWDrawer('editPWDrawer', 'editPWOverlay')">
                                <img class="touch-ic secondary-icon" src="./img/tag-filled.svg">
                            </div>
                            <span class="desktop-only pw__esc secondary-hover fs-md" onclick="closePW()">esc</span>
                        </div>
                        <!--pw Content-->
                        <div id="editFormBody" class="pw__content">
                            <textarea id="textpage" class="pw__content-body secondary-scroll editForm__textarea fs-md" onchange="resizeTextPage()" title="Source code of page"></textarea>

                            <div class="pw__floatbar">
                                <form onsubmit="document.getElementById('editForm-save').onclick();"><input id="summaryedit" class="secondary-placeholder fs-md" title="Summary" placeholder="Briefly describe your changes."></form>
                                <div id="editForm-save" class="secondary-hover" title="Publish changes" ng-click="doEdit()" onclick="closePW()">
                                    <img class="touch-ic secondary-icon" src="./img/save-filled.svg" alt="Save image">
                                </div>
                            </div>
                        </div>
                        <!--pw Drawer-->
                        <div id="editPWDrawer" class="pw__drawer secondary-scroll">
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">Tags</span>
                            </div>
                            <div id="btn-group-delete" class="pw__drawer__content">
                                <div class="i__base">
                                    <div class="i__title fs-md">Warn user</div>
                                    <div class="i__description fs-xs">Turning this on will left a notification on the user talk page after clicking templates (green only).</div>
                                    <div class="i__content fs-sm">
                                        <div id="warn-box-delete" class="t-btn__secondary"></div>
                                    </div>
                                </div>
                                <div ng-repeat="speedy in speedys track by $index" onclick="queueClick()">
                                    <a class="fs-sm" style="cursor: pointer;" ng-click="selectSpeedy(speedy)" ng-style="speedyColor(speedy)">{{speedy.name}}</a>
                                </div>
                            </div>
                        </div>
                        <!--pw Overlay-->
                        <div id="editPWOverlay" class="pw__overlay" onclick="closePWDrawer('editPWDrawer', 'editPWOverlay')"></div>
                    </div>

                    <!-- Settings | popup-window -->
                    <div id="settings" class="pw__base" style="display: none;">
                        <!--pw Header-->
                        <div class="pw__header action-header">
                            <div class="mobile-only secondary-hover" onclick="openSidebar();">
                                <img class="touch-ic secondary-icon" src="./img/drawer-filled.svg" alt="Box Image">
                            </div>
                            <span class="action-header__title fs-xl">Settings</span>
                            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePW()">
                                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
                            </div>
                            <div class="mobile-only secondary-hover" onclick="openPWDrawer('settingsPWDrawer', 'settingsPWOverlay')">
                                <img class="touch-ic secondary-icon" src="./img/v-dots-filled.svg">
                            </div>
                            <span class="desktop-only pw__esc secondary-hover fs-md" onclick="closePW()">esc</span>
                        </div>
                        <!--pw Content-->
                        <div class="pw__content">
                            <div class="pw__content-body secondary-scroll">
                                <div class="action-header">
                                    <span class="action-header__title fs-lg">Customization</span>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Theme</div>
                                    <div class="i__description fs-xs">Change theme.</div>
                                    <div class="i__content fs-sm">
                                        <select id="themeSelector" class="i-select__secondary fs-md"></select>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Sound</div>
                                    <div class="i__description fs-xs">Change sound mode.</div>
                                    <div class="i__content fs-sm">
                                    <select id="soundSelector" class="i-select__secondary fs-md">
                                        <option value="0">None</option>
                                        <option value="1">All sounds</option>
                                        <option value="2">Msg & mentions</option>
                                        <option value="3">Only mentions</option>
                                        <option value="4">Edits & mentions</option>
                                        <option value="5">Only Edits</option>
                                    </select>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Bottom-up</div>
                                    <div class="i__description fs-xs">Show edits from bottom to up direction in queue.</div>
                                    <div class="i__content fs-sm">
                                        <div id="bottom-up-btn" class="t-btn__secondary" onclick="toggleTButton(this); bottomUp(this);"></div>
                                    </div>
                                </div>
                                <div class="desktop-only i__base">
                                    <div class="i__title fs-md">RH mode</div>
                                    <div class="i__description fs-xs">Show queue on the right hand side.</div>
                                    <div class="i__content fs-sm">
                                        <div id="RH-mode-btn" class="t-btn__secondary" onclick="toggleTButton(this); RHModeBtn(this, false);"></div>
                                    </div>
                                </div>

                                <div class="action-header">
                                    <span class="action-header__title fs-lg">Behaviour</span>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Registered</div>
                                    <div class="i__description fs-xs">Enable edits from registered users.</div>
                                    <div class="i__content fs-sm">
                                        <div id="registered-btn" class="t-btn__secondary" onclick="toggleTButton(this); registeredBtn(this);"></div>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Anonymous</div>
                                    <div class="i__description fs-xs">Enable edits from anonymous users.</div>
                                    <div class="i__content fs-sm">
                                        <div id="onlyanons-btn" class="t-btn__secondary" onclick="toggleTButton(this); onlyAnonsBtn(this);"></div>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">New pages</div>
                                    <div class="i__description fs-xs">Enable new pages creations.</div>
                                    <div class="i__content fs-sm">
                                        <div id="new-pages-btn" class="t-btn__secondary" onclick="toggleTButton(this); newPagesBtn(this);"></div>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Only new pages</div>
                                    <div class="i__description fs-xs">Enable only new page creations.</div>
                                    <div class="i__content fs-sm">
                                        <div id="onlynew-pages-btn" class="t-btn__secondary" onclick="toggleTButton(this); onlyNewPagesBtn(this);"></div>
                                    </div>
                                </div>
                                <?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
                                <div class="i__base">
                                    <div class="i__title fs-md">Small Wikis</div>
                                    <div class="i__description fs-xs">Enable edits from small wikis.</div>
                                    <div class="i__content fs-sm">
                                        <div id="small-wikis-btn" class="t-btn__secondary" onclick="toggleTButton(this); smallWikisBtn(this);"></div>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Less then 300 users</div>
                                    <div class="i__description fs-xs">Enable edits from wikis with less then 300 active users.</div>
                                    <div class="i__content fs-sm">
                                        <div id="lt-300-btn" class="t-btn__secondary" onclick="toggleTButton(this); lt300Btn(this);"></div>
                                    </div>
                                </div>
                                ';}?>
                                <div class="i__base">
                                    <div class="i__title fs-md">Queue limit</div>
                                    <div class="i__description fs-xs">Max count of edits allowed to load in queue.</div>
                                    <div class="i__content fs-sm">
                                        <input id="max-queue" class="i-input__secondary secondary-placeholder fs-sm" name="max-queue" placeholder="No limit">
                                    </div>
                                </div>

                                <div class="action-header">
                                    <span class="action-header__title fs-lg">Filters</span>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Edits limit</div>
                                    <div class="i__description fs-xs">Number of edits after which edits of user will be whitelisted.</div>
                                    <div class="i__content fs-sm">
                                        <input id="max-edits" class="i-input__secondary secondary-placeholder fs-sm" name="max-edits" placeholder="Max edits">
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Days limit</div>
                                    <div class="i__description fs-xs">Account age in days after which edits of user will be whitelisted.</div>
                                    <div class="i__content fs-sm">
                                        <input id="max-days" class="i-input__secondary secondary-placeholder fs-sm" name="max-days" placeholder="Max days">
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Namespace filter</div>
                                    <div class="i__description fs-xs">Add <a style="display: inline;" href="https://en.wikipedia.org/wiki/Help:MediaWiki_namespace" rel="noopener noreferrer" target="_blank">namespace</a> to filter edits in queue.</div>
                                    <div class="i__content fs-sm">
                                        <div id="btn-delete-ns" class="i-minus fs-sm">-</div>
                                        <input id="ns-input" class="i-input__secondary secondary-placeholder fs-sm" name="" placeholder="Enter">
                                        <div id="btn-add-ns" class="i-plus fs-sm">+</div>
                                    </div>
                                    <div class="i__extra">
                                        <ul id="nsList" class="i-chip-list fs-sm"></ul>
                                    </div>
                                </div>
                                <?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
                                <div class="i__base">
                                    <div class="i__title fs-md">Custom wikis</div>
                                    <div class="i__description fs-xs">Add your home-wiki or wikis which are not in small wikis list.</div>
                                    <div class="i__content fs-sm">
                                        <div id="btn-bl-p-delete" class="i-minus fs-sm">-</div>
                                        <input id="bl-p" class="i-input__secondary secondary-placeholder fs-sm" name="bl-p" placeholder="Enter">
                                        <div id="btn-bl-p-add" class="i-plus fs-sm">+</div>
                                    </div>
                                    <div class="i__extra">
                                        <ul id="blareap" class="i-chip-list fs-sm"></ul>
                                    </div>
                                </div>
                                ';}?>
                                <div class="i__base">
                                    <div class="i__title fs-md">Users whitelist</div>
                                    <div class="i__description fs-xs">Add users to skip their edits from queue.</div>
                                    <div class="i__content fs-sm">
                                        <div id="btn-wl-u-delete" class="i-minus fs-sm">-</div>
                                        <input id="wladdu" class="i-input__secondary secondary-placeholder fs-sm" name="wladdu" placeholder="Enter">
                                        <div id="btn-wl-u-add" class="i-plus fs-sm">+</div>
                                    </div>
                                    <div class="i__extra">
                                        <ul id="wlareau" class="i-chip-list fs-sm"></ul>
                                    </div>
                                </div>
                                <div class="i__base">
                                    <div class="i__title fs-md">Wikis whitelist</div>
                                    <div class="i__description fs-xs">Add wikis to skip their edits from queue.</div>
                                    <div class="i__content fs-sm">
                                        <div id="btn-wl-p-delete" class="i-minus fs-sm">-</div>
                                        <input id="wladdp" class="i-input__secondary secondary-placeholder fs-sm" name="wladdp" placeholder="Enter">
                                        <div id="btn-wl-p-add" class="i-plus fs-sm">+</div>
                                    </div>
                                    <div class="i__extra">
                                        <ul id="wlareap" class="i-chip-list fs-sm"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--pw Drawer-->
                        <div id="settingsPWDrawer" class="pw__drawer secondary-scroll">
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">Info</span>
                            </div>
                            <div class="pw__drawer__content">
                                <a class="fs-sm" id="luxo" href='https://tools.wmflabs.org/guc/?by=date&user={{selected.user}}' rel='noopener noreferrer' target='_blank'>Global contribs</a>
                                <a class="fs-sm" href='https://meta.wikimedia.org/wiki/Special:CentralAuth?target={{selected.user}}' rel='noopener noreferrer' target='_blank'>Central auth</a>
                            </div>
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">Reports</span>
                            </div>
                            <div class="pw__drawer__content">
                                <a class="fs-sm" href='https://meta.wikimedia.org/wiki/Meta:Requests_for_help_from_a_sysop_or_bureaucrat' rel='noopener noreferrer' target='_blank'>Meta:RFH</a>
                                <a class="fs-sm" href='https://meta.wikimedia.org/wiki/Steward_requests/Miscellaneous' rel='noopener noreferrer' target='_blank'>SRM</a>
                            </div>
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">Scripts, templates</span>
                            </div>
                            <div class="pw__drawer__content">
                                <a class="fs-sm" href='https://meta.wikimedia.org/wiki/User:Hoo_man/Scripts/Tagger' rel='noopener noreferrer' target='_blank'>Tagger</a>
                                <a class="fs-sm" href='https://meta.wikimedia.org/wiki/User:Syum90/Warning_templates' rel='noopener noreferrer' target='_blank'>Warnings</a>
                            </div>
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">Translators</span>
                            </div>
                            <div class="pw__drawer__content">
                                <a class="fs-sm" href='https://translate.google.com/#auto/en/' rel='noopener noreferrer' target='_blank'>Google translator</a>
                                <a class="fs-sm" href='https://translate.yandex.com/' rel='noopener noreferrer' target='_blank'>Yandex translator</a>
                                <a class="fs-sm" href='http://www.online-translator.com' rel='noopener noreferrer' target='_blank'>Promt translator</a>
                                <a class="fs-sm" href='https://www.bing.com/translator' rel='noopener noreferrer' target='_blank'>Bing translator</a>
                                <a class="fs-sm" href='https://www.deepl.com/en/translator' rel='noopener noreferrer' target='_blank'>DeepL translator</a>
                            </div>
                            <div class="action-header__sticky">
                                <span class="action-header__title fs-lg">Contact</span>
                            </div>
                            <div class="pw__drawer__content">
                                <a class="fs-sm" href='http://webchat.freenode.net/?channels=%23countervandalism%2C%23cvn-sw' rel='noopener noreferrer' target='_blank'>IRC</a>
                                <a class="fs-sm" href='https://discord.gg/UTScYTR' rel='noopener noreferrer' target='_blank'>Discord</a>
                            </div>
                            <?php if ($userSelf == "Ajbura" || $userSelf == "Iluvatar" || $userSelf == "1997kB") {
                                echo '
                                <div class="action-header__sticky">
                                    <span class="action-header__title fs-lg">Control SWV</span>
                                </div>
                                <div class="pw__drawer__content">
                                    <a class="fs-sm" href="https://tools.wmflabs.org/swviewer/php/control.php" rel="noopener noreferrer" target="_blank">Control panel</a>
                                </div>';
                            }?>
                            
                        </div>
                        <!--pw Overlay-->
                        <div id="settingsPWOverlay" class="pw__overlay" onclick="closePWDrawer('settingsPWDrawer', 'settingsPWOverlay')"></div>
                    </div>

                    <!-- floating overlay --> 
                    <div id="floatingOverlay" class="floating-overlay"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- customRevert | Popup-overlay -->
    <div id="customRevert" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">Custom revert</span>
            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePO()">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <form id="summariesContainer" style="display: flex" ng-submit="Revert();">
                    <input class="i-input__secondary secondary-placeholder fs-md" style="margin-right: 8px;" title="Reason" name="credit" id="credit" placeholder="Provide a reason."/>
                    <button type="button" class="i-btn__accent accent-hover fs-md" id="btn-cr-u-apply" ng-click="Revert();">Revert</button>
                </form>
                <br>
                <div class="i__base">
                    <div class="i__title fs-md">Warn user</div>
                    <div class="i__description fs-xs">Turning this on will left a warning on the user talk page after clicking common summaries (green only).</div>
                    <div class="i__content fs-sm">
                        <div id="warn-box" class="t-btn__secondary"></div>
                    </div>
                </div>
                <label class="fs-md">Common Summaries:</label>
                <div class="panel-cr-reasons" ng-repeat="description in descriptions track by $index" onclick="queueClick()">
                    <div class="fs-sm" ng-style="descriptionColor(description)" ng-click="selectDescription(description)">{{description.name}}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Local requests | Popup-overlay -->
    <div id="localRequests" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">Local Requests</span>
            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePO()">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll" style="padding-top: 0;">
                <div id="reportDiffsLocal">
                    <label class="fs-md" style="padding-top: var(--side-padding);">Local blocks</label>
                    <input id="reportHeaderLocal"class="i-input__secondary secondary-placeholder fs-md" type="text"/>
                    <textarea id="reportCommentLocal" class="i-textarea__secondary secondary-scroll fs-sm"></textarea>
                    <div id="request-btn-block-send-l" class="i-btn__accent accent-hover fs-md" ng-click="sendReportLocal();">Send</div>
                </div>
                <div id="protectDiffsLocal">
                    <label class="fs-md" style="padding-top: var(--side-padding);">Local protect</label>
                    <input id="protectHeaderLocal"class="i-input__secondary secondary-placeholder fs-md" type="text"/>
                    <textarea id="protectCommentLocal" class="i-textarea__secondary secondary-scroll fs-sm"></textarea>
                    <div id="request-btn-protect-send-l" class="i-btn__accent accent-hover fs-md" ng-click="sendRequestProtect();">Send</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Global requests | Popup-overlay -->
    <div id="globalRequests" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">Global Requests</span>
            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePO()">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll" style="padding-top: 0;">
                <div id="reportDiffs">
                    <label class="fs-md" style="padding-top: var(--side-padding);">Global locks</label>
                    <input id="reportHeader"class="i-input__secondary secondary-placeholder fs-md" type="text"/>
                    <textarea id="reportComment" class="i-textarea__secondary secondary-scroll fs-sm"></textarea>
                    <div id="request-btn-block-send-g" class="i-btn__accent accent-hover fs-md" ng-click="reqBlockG();">Send</div>
                </div>
                <div id="othersDiffsGlobal">
                    <label class="fs-md" style="padding-top: var(--side-padding);">Global miscellaneous</label>
                    <input id="othersHeaderGlobal"class="i-input__secondary secondary-placeholder fs-md" type="text"/>
                    <textarea id="othersCommentGlobal" class="i-textarea__secondary secondary-scroll fs-sm"></textarea>
                    <div id="request-btn-others-send-g" class="i-btn__accent accent-hover fs-md" ng-click="reqOthersG();">Send</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global requests | Popup-overlay -->
    <div id="about" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">About</span>
            <div class="mobile-only secondary-hover" title="Close [esc]" onclick="closePO()">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <div class="fs-md">
                        SWViewer: see <a href='https://meta.wikimedia.org/wiki/SWViewer' rel='noopener noreferrer' target='_blank'>documentation page</a>.<br><br>
    
                        Any questions, please <a href='https://meta.wikimedia.org/wiki/Talk:SWViewer' rel='noopener noreferrer' target='_blank'>ask here</a>.<br><br>
    
                        Privacy: Application saves only name of your account, your internal app settings, your actions via app (see 'logs'), count of logins to app, count of app opens, date of last login to app and contents of the chat.<br><br>
    
                        Licensing:<br>
                        Copyright &#169; mainteiners of SWViewer, 2017-2019<br><br>
    
                        Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the 'Software'), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:<br><br>
    
                        The above copyright notice and this permission notice shall be included in all
                        copies or substantial portions of the Software.<br><br>
    
                        THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.<br><br>
    
                        bump.mp3:<br>
                        <a rel='noopener noreferrer' target='_blank' href='https://freesound.org/people/Porphyr/sounds/208435/'>license and author of original file</a><br>
                        message.mp3:<br>
                        <a rel='noopener noreferrer' target='_blank' href='https://freesound.org/people/elmasmalo1/sounds/377017/'>license and author of original file</a><br>
                        privateMessage.mp3:<br>
                        <a rel='noopener noreferrer' target='_blank' href='https://freesound.org/people/rhodesmas/sounds/342749/'>license and author of original file</a><br>
                        Auth and some API-requests are based on a <a rel='noopener noreferrer' target='_blank' href='https://tools.wmflabs.org/oauth-hello-world/index.php?action=download'>that code</a><br>
                 </div>
            </div>
        </div>
    </div>

    <!-- po Overlay-->
    <div id="POOverlay" class="po__overlay" onclick="closePO()"></div>
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
var sound = 0;
var newSound;
var messageSound;
var privateMessageSound;
firstClick = false;

document.getElementById("mainapp-body").onclick = function() {
    if (firstClick === false) {
        firstClick = true;
        messageSound = new Audio("sounds/message.mp3");
        privateMessageSound = new Audio("sounds/privateMessage.mp3");
        newSound = new Audio("sounds/bump.mp3");
        messageSound.load();
        privateMessageSound.load();
        newSound.load();
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
        toggleTButton(document.getElementById('small-wikis-btn'));
    if (settingslist['swmt'] === '2' && isGlobalModeAccess === true) {
        toggleTButton(document.getElementById('small-wikis-btn'));
}

if (settingslist['users'] !== null && (typeof settingslist['users'] !== 'undefined') && settingslist['users'] !== '') {
    if ((settingslist['users'] === '1' || settingslist['users'] === '2') && isGlobal === true)
        toggleTButton(document.getElementById('lt-300-btn'));
    if (settingslist['users'] == '2' && isGlobalModeAccess === true)
        toggleTButton(document.getElementById('lt-300-btn'));
}
"; } ?>

if (settingslist['registered'] !== null && (typeof settingslist['registered'] !== "undefined") && settingslist['regustered'] !== "") {
    if (settingslist['registered'] === "1")
        toggleTButton(document.getElementById('registered-btn'));
}

 if (settingslist['new'] !== null && (typeof settingslist['new'] !== "undefined") && settingslist['new'] !== "") {
    if (settingslist['new'] === "1")
        toggleTButton(document.getElementById('new-pages-btn'));
 }

if (settingslist['onlynew'] !== null && (typeof settingslist['onlynew'] !== "undefined") && settingslist['onlynew'] !== "") {
    if (settingslist['onlynew'] === "1")
        toggleTButton(document.getElementById('onlynew-pages-btn'));
}

if (settingslist['onlyanons'] !== null && (typeof settingslist['onlyanons'] !== "undefined") && settingslist['onlyanons'] !== "") {
    if (settingslist['onlyanons'] === "1")
        toggleTButton(document.getElementById('onlyanons-btn'));
}

if (settingslist['direction'] !== null && (typeof settingslist['direction'] !== "undefined") && settingslist['direction'] !== "") {
    if (settingslist['direction'] === "1") {
        document.getElementById("queue").setAttribute("style", "display:flex; flex-direction:column-reverse");
        toggleTButton(document.getElementById('bottom-up-btn'));
    }
}

if (settingslist['rhand'] !== null && (typeof settingslist['rhand'] !== "undefined") && settingslist['rhand'] !== "") {
    if (settingslist['rhand'] === "1") {
        toggleTButton(document.getElementById("RH-mode-btn"));
        RHModeBtn(document.getElementById('RH-mode-btn'), true);
    }
}

if (settingslist['mobile'] !== null && (typeof settingslist['mobile'] !== "undefined") && settingslist['mobile'] !== "") {
    if (settingslist['mobile'] === "1" || settingslist['mobile'] === "2" || settingslist['mobile'] === "3" || settingslist['mobile'] === "0")
        resizeDrawer(Number(settingslist['mobile']), true);
}

if (settingslist['sound'] !== null && (typeof settingslist['sound'] !== "undefined") && settingslist['sound'] !== "") {
    sound = Number(settingslist['sound']);
    document.getElementById("soundSelector").value = sound;
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
        '--bc-primary': '#191919', '--bc-primary-low': '#212121', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-secondary': '#FFFFFF', '--bc-secondary-low': '#F4F4F4', '--bc-secondary-hover': 'rgba(0, 0, 0, .1)',
        '--bc-accent': '#0063E4', '--bc-accent-hover': '#0056C7',
        '--bc-positive': 'rgb(36, 164, 100)', '--bc-negative': 'rgb(251, 47, 47)',
        '--ic-primary': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--ic-secondary': 'invert(0.30) sepia(1) saturate(0) hue-rotate(200deg)',
        '--ic-accent': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--tc-primary': 'rgba(255, 255, 255, 1)', '--tc-primary-low': 'rgba(255, 255, 255, .8)',
        '--tc-secondary': 'rgba(0, 0, 0, 1)', '--tc-secondary-low': 'rgba(0, 0, 0, .7)',
        '--tc-accent': 'rgba(255, 255, 255, 1)',
        '--link-color': '#337ab7', '--tc-positive': 'var(--bc-positive)', '--tc-negative': 'var(--bc-negative)',
        '--fs-xl': '26px', '--fs-lg': '18px', '--fs-md': '16px', '--fs-sm': '14px', '--fs-xs': '11px',
        '--lh-xl': '1.125', '--lh-lg': '1.25', '--lh-md': '1.5', '--lh-sm': '1.5', '--lh-xs': '1.5',
    },
    "Light": {
        '--bc-primary': '#f6f6f6', '--bc-primary-low': '#efefef', '--bc-primary-hover': 'rgba(0, 0, 0, .1)',
        '--bc-secondary': '#FFFFFF', '--bc-secondary-low': '#F4F4F4', '--bc-secondary-hover': 'rgba(0, 0, 0, .1)',
        '--bc-accent': '#0063E4', '--bc-accent-hover': '#0056C7',
        '--bc-positive': 'rgb(36, 164, 100)', '--bc-negative': 'rgb(251, 47, 47)',
        '--ic-primary': 'invert(0.30) sepia(1) saturate(0) hue-rotate(200deg)',
        '--ic-secondary': 'invert(0.30) sepia(1) saturate(0) hue-rotate(200deg)',
        '--ic-accent': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--tc-primary': 'rgba(0, 0, 0, 1)', '--tc-primary-low': 'rgba(0, 0, 0, .7)',
        '--tc-secondary': 'rgba(0, 0, 0, 1)', '--tc-secondary-low': 'rgba(0, 0, 0, .7)',
        '--tc-accent': 'rgba(255, 255, 255, 1)',
        '--link-color': '#337ab7', '--tc-positive': 'var(--bc-positive)', '--tc-negative': 'var(--bc-negative)',
        '--fs-xl': '26px', '--fs-lg': '18px', '--fs-md': '16px', '--fs-sm': '14px', '--fs-xs': '11px',
        '--lh-xl': '1.125', '--lh-lg': '1.25', '--lh-md': '1.5', '--lh-sm': '1.5', '--lh-xs': '1.5',
    },
    "Dark": {
        '--bc-primary': '#0f1115', '--bc-primary-low': '#15171d', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-secondary': '#1c1e26', '--bc-secondary-low': '#21242c', '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-accent': '#0050b8', '--bc-accent-hover': '#003c8a',
        '--bc-positive': 'rgb(36, 164, 100)', '--bc-negative': 'rgb(251, 47, 47)',
        '--ic-primary': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--ic-secondary': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--ic-accent': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--tc-primary': 'rgba(255, 255, 255, 1)', '--tc-primary-low': 'rgba(255, 255, 255, .8)',
        '--tc-secondary': 'rgba(255, 255, 255, 1)', '--tc-secondary-low': 'rgba(255, 255, 255, .8)',
        '--tc-accent': 'rgba(255, 255, 255, 1)',
        '--link-color': '#337ab7', '--tc-positive': 'var(--bc-positive)', '--tc-negative': 'var(--bc-negative)',
        '--fs-xl': '26px', '--fs-lg': '18px', '--fs-md': '16px', '--fs-sm': '14px', '--fs-xs': '11px',
        '--lh-xl': '1.125', '--lh-lg': '1.25', '--lh-md': '1.5', '--lh-sm': '1.5', '--lh-xs': '1.5',
    },
    "AMOLED": {
        '--bc-primary': '#000000', '--bc-primary-low': '#050505', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-secondary': '#000000', '--bc-secondary-low': '#111111', '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-accent': '#0050b8', '--bc-accent-hover': '#003c8a',
        '--bc-positive': 'rgb(36, 164, 100)', '--bc-negative': 'rgb(251, 47, 47)',
        '--ic-primary': 'invert(0.85) sepia(1) saturate(68) hue-rotate(175deg)',
        '--ic-secondary': 'invert(0.85) sepia(1) saturate(68) hue-rotate(175deg)',
        '--ic-accent': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)',
        '--tc-primary': 'rgba(198, 225, 255, 1)', '--tc-primary-low': 'rgba(198, 225, 255, .8)',
        '--tc-secondary': 'rgba(198, 225, 255, 1)', '--tc-secondary-low': 'rgba(198, 225, 255, .8)',
        '--tc-accent': 'rgba(255, 255, 255, 1)',
        '--link-color': '#337ab7', '--tc-positive': 'var(--bc-positive)', '--tc-negative': 'var(--bc-negative)',
        '--fs-xl': '26px', '--fs-lg': '18px', '--fs-md': '16px', '--fs-sm': '14px', '--fs-xs': '11px',
        '--lh-xl': '1.125', '--lh-lg': '1.25', '--lh-md': '1.5', '--lh-sm': '1.5', '--lh-xs': '1.5',
    }
}

function loadThemeList() {
    for(name in Object.keys(THEME)) {
        var option = document.createElement('option');
        option.innerHTML = Object.keys(THEME)[name];
        document.getElementById('themeSelector').appendChild(option);
    }
};
function getStrTheme(THEME) {
    let strTheme = '{';
    Object.keys(THEME).forEach((item) => {
        strTheme = strTheme + item + ':' + THEME[item] + ';';
    });
    return strTheme + '}';
}
function setStrTheme(str, THEME) {
    var newFront = str.substring( 0, str.indexOf(":root") + ":root".length);
    var remain = str.substring(str.indexOf(":root") + ":root".length, str.length);
    var newEnd = remain.substring(remain.indexOf('}') + 1, remain.length);

    return newFront + THEME + newEnd;
};
function setTheme(THEME) {
    let root = document.documentElement;

    Object.keys(THEME).forEach((item) => {
        root.style.setProperty(item, THEME[item]);
    });
    
    /*-----chrome address bar color-------*/
    $('meta[name=theme-color]').attr('content', THEME['--bc-primary']);

    /*-----Send theme to iframes-------*/
    let strTheme = getStrTheme(THEME);

    // var welcomeIF = document.getElementById("page-welcome").contentWindow;
    // welcomeIF.postMessage(THEME, "*");

    diffstart = setStrTheme(diffstart, strTheme);
    newstart = setStrTheme(newstart, strTheme);
    starterror = setStrTheme(starterror, strTheme);
    if(document.getElementById("page").srcdoc != "") {
        document.getElementById("page").srcdoc = setStrTheme(document.getElementById("page").srcdoc, strTheme);
    }
};
function changeTheme(select) { setTheme(THEME[Object.keys(THEME)[select]]); };

/*------Document variables------*/
const $descriptionContainer = document.getElementById('description-container');
const $queueDrawer = document.getElementById('queueDrawer');
const $floatingOverlay = document.getElementById('floatingOverlay');
const $sidebar = document.getElementById('sidebar');

const $btnDrawer = document.getElementById('btn-drawer');

/*------Sidebar-----*/
function openSidebar () {
    $sidebar.classList.add('sidebar-base__floating');
    $floatingOverlay.classList.add('floating-overlay__active');
}
function closeSidebar () {
    $sidebar.classList.remove('sidebar-base__floating');
    $floatingOverlay.classList.remove('floating-overlay__active');
}

$floatingOverlay.addEventListener('click', () => closeSidebar());

/*------drawer-btn-------*/
var mDrawer;
$btnDrawer.addEventListener('click', () => {
    resizeDrawer(mDrawer, false);
})

function resizeDrawer(state, start) {
    mDrawer = state;
    if (start === true) {
        switch(mDrawer) {
            case 0:
                mDrawer = 2;
                break;
            case 1:
                mDrawer = 0;
                break;
            case 2:
                mDrawer = 1;
                break;
        }
    }
    resizeIFrame(document.getElementById('page'));
    switch (mDrawer) {
        case 0:
            document.getElementById('next-diff').style.transform = "scale(0)";
            document.documentElement.style.setProperty('--m-queue-drawer-width', '48px');
            mDrawer = 1;
            break;
        case 1:
            document.documentElement.style.setProperty('--m-queue-drawer-width', '90px');
            mDrawer = 2;
            break;
        case 2:
            document.documentElement.style.setProperty('--m-queue-drawer-width', '0px');
            mDrawer = 0;
            document.getElementById('next-diff').style.transform = "scale(1)";
            break;
    }
    if (start !== true)
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: { 'action': 'set', query: 'mobile', mobile: mDrawer }, dataType: 'json'});
};

function togglMoreControl () {
    var mc = document.getElementById('moreControl');
    var mcOverlay = document.getElementById('moreControlOverlay');
    if (mc.classList.contains('more-control__hidden')) {
        mc.classList.remove('more-control__hidden');
        mcOverlay.classList.add('more-control__overlay__active');
    } else {
        mc.classList.add('more-control__hidden');
        mcOverlay.classList.remove('more-control__overlay__active');
    }
}

/*------ Diff viewer -----*/

function resizeIFrame (iFrame) {
    setTimeout(() => {
        iFrame.style.height = 'calc(' + iFrame.contentWindow.document.body.scrollHeight + 'px + 3 * var(--side-padding) + var(--floatbar-height) + 56px)';
        iFrame.parentElement.style.paddingTop = $descriptionContainer.offsetHeight + 'px';
        $descriptionContainer.style.marginTop = '0px';
    }, 0);
}

document.getElementById('page').onload = function () {
    resizeIFrame(this);
    this.parentElement.scrollTop = 0;
}

var prevScroll = 0;
function hideDesc (el) {
    if (el.scrollTop > prevScroll && el.scrollTop > $descriptionContainer.offsetHeight) {
        $descriptionContainer.style.marginTop = (-1 * ($descriptionContainer.offsetHeight + 1)) + 'px';
    } else {
        $descriptionContainer.style.marginTop = '0px';
    }
    prevScroll = el.scrollTop;
}

/*###################
------PW and PO Module------
#####################*/
var lastOpenedPW = undefined;
function toggleTab (oldTab, newTab) {
    const close = (tab) => {
        if (tab === undefined) document.getElementById('btn-home').classList.remove('tab__active');
        else if (tab === 'talkForm') document.getElementById('btn-talk').classList.remove('tab__active');
        else if (tab === 'logs') document.getElementById('btn-logs').classList.remove('tab__active');
        else if (tab === 'settings') document.getElementById('btn-settings').classList.remove('tab__active');
    }
    const open = (tab) => {
        if (tab === undefined) document.getElementById('btn-home').classList.add('tab__active');
        else if (tab === 'talkForm') document.getElementById('btn-talk').classList.add('tab__active');
        else if (tab === 'logs') document.getElementById('btn-logs').classList.add('tab__active');
        else if (tab === 'settings') document.getElementById('btn-settings').classList.add('tab__active');
    }
    close(oldTab);
    open(newTab);
}
function openPW (pw) {
    if (pw !== lastOpenedPW) {
        if (lastOpenedPW !== undefined) closePW(true);
        toggleTab(lastOpenedPW, pw);
        lastOpenedPW = pw;
        document.getElementById(pw).style.display = 'grid';
        closeSidebar();

        if (pw === 'talkForm') onTalkOpen();
    }
}
function closePW (dontToggle) {
    if (lastOpenedPW !== undefined) {
        document.getElementById(lastOpenedPW).style.display = 'none';
        if (!dontToggle) {
            toggleTab(lastOpenedPW, undefined);
            lastOpenedPW = undefined;
            closeSidebar();
        }

        if (lastOpenedPW == 'settings') onSettingsClose();
    }
}
function openPWDrawer (drawer, overlay) {
    document.getElementById(drawer).classList.add('pw__drawer__active');
    document.getElementById(overlay).classList.add('pw__overlay__active');
}
function closePWDrawer (drawer, overlay) {
    document.getElementById(drawer).classList.remove('pw__drawer__active');
    document.getElementById(overlay).classList.remove('pw__overlay__active');
}

var lastOpenedPO = undefined;
function openPO (po) {
    document.getElementById(po).classList.add('po__active');
    document.getElementById('POOverlay').classList.add('po__overlay__active');
    lastOpenedPO = po;
}
function closePO () {
    if (lastOpenedPO !== undefined) {
        document.getElementById(lastOpenedPO).classList.remove('po__active');
        document.getElementById('POOverlay').classList.remove('po__overlay__active');
    }
}

/*###################
------- Common -------
#####################*/

function scrollToBottom(id){
   var element = document.getElementById(id);
   element.scrollTop = element.scrollHeight;
};

function queueClick() {
    if (document.getElementById('settings').style.display == "block")
        closeSettingsSend();

    closePW();
}

function toggleTButton (button) {
    if (button.classList.contains('t-btn__active')) {
        return button.classList.remove('t-btn__active');
    }
    button.classList.add('t-btn__active');
}

window.onresize = function() {
    resizeIFrame(document.getElementById('page'));
}


/*######################
------- Settings -------
######################*/

const onSettingsClose = () => {
    closeSettingsSend();
    document.getElementById('max-days').value = regdays;
    document.getElementById('max-edits').value = countedits;
    if (countqueue == 0)
        document.getElementById('max-queue').value = "";
    else
        document.getElementById('max-queue').value = countqueue;
}

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

document.getElementById('themeSelector').onchange = function() {
    changeTheme(document.getElementById('themeSelector').selectedIndex);

    $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
        action: 'set',
        query: 'theme',
        limit: Object.keys(THEME),
        theme: document.getElementById("themeSelector").selectedIndex
    }, dataType: 'json'});
};

function bottomUp (button) {
        var sendDirection;
        if (button.classList.contains('t-btn__active')) {
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

$("#soundSelector").on("change", function() {
    sound = Number($(this).val());
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'sound',
            sound: sound
        }, dataType: 'json'});
});

function RHModeBtn (button, start) {
    var rhmode;
    if (button.classList.contains('t-btn__active')) {
        document.getElementById('baseGrid').classList.add('base-grid__RH-mode');
        rhmode = 1;
    } else {
        document.getElementById('baseGrid').classList.remove('base-grid__RH-mode');
        rhmode = 0
    }
    if (start === false)
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'rhand',
            rhand: rhmode
        }, dataType: 'json'});
}

function registeredBtn (button) {
        var sqlreg = 0;
        if (button.classList.contains('t-btn__active'))
            sqlreg = 1;
        else {
            if (!document.getElementById('onlyanons-btn').classList.contains('t-btn__active'))
                document.getElementById('onlyanons-btn').click();
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'registered',
            registered: sqlreg
        }, dataType: 'json'});
};

function newPagesBtn (button) {
        var sqlnew = 0;
        if (button.classList.contains('t-btn__active'))
            sqlnew = 1;
        else {
            if (document.getElementById('onlynew-pages-btn').classList.contains('t-btn__active'))
                document.getElementById('onlynew-pages-btn').click();
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'newbies',
            sqlnew: sqlnew
        }, dataType: 'json'});
};

function onlyNewPagesBtn(button) {
        var onlynew = 0;
        if (button.classList.contains('t-btn__active')) {
            onlynew = 1;
            if (!document.getElementById('new-pages-btn').classList.contains('t-btn__active'))
                document.getElementById('new-pages-btn').click();
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'onlynew',
            onlynew: onlynew
        }, dataType: 'json'});

};

function onlyAnonsBtn(button) {
        var onlyanons = 0;
        if (button.classList.contains('t-btn__active'))
            onlyanons = 1;
        else {
            if (!document.getElementById('registered-btn').classList.contains('t-btn__active'))
                document.getElementById('registered-btn').click();
        }
        $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: {
            action: 'set',
            query: 'anons',
            anons: onlyanons
        }, dataType: 'json'});

};

<?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo "
function smallWikisBtn (button) {
        var sqlswmt = 0;
        if (button.classList.contains('t-btn__active')) {
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

function lt300Btn (button) {
        var sqlusers = 0;
        if (button.classList.contains('t-btn__active')) {
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
    audiopromise = ps.play();
    if (audiopromise !== undefined)
        audiopromise.then( function() { return null; }).catch( function() { return null; });
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

function onTalkOpen () {
    scrollToBottom("talk-content");
    if (document.getElementById('badge-talk').style.background !== "rgb(251, 47, 47)") {
        document.getElementById('badge-talk').style.background = "none";
        document.getElementById('badge-talk-ex1').style.background = "none";
        document.getElementById('badge-talk').classList.add('badge-ic__primary');
        document.getElementById('badge-talk-ex1').classList.add('badge-ic__primary');
    }
}

var userColor = new Map();
function getUserColor(user) {
    if(!userColor.has(user)) userColor.set(user, `hsl(${Math.floor(Math.random() * 361)}, ${(Math.floor(Math.random() * 50) + 40)}%, 50%`);
    return userColor.get(user);
}

function parseDate(date) {
  const parsed = Date.parse(date);
  if (!isNaN(parsed)) {
    return parsed;
  }

  return Date.parse(date.replace(/-/g, '/').replace(/[a-z]+/gi, ' '));
}
var lastMsg = { user: undefined, time: {hours: undefined, minuts: undefined} };
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

        var textTime =hours + ':' + minuts;
        var textUser = nickname;
        var textMessage = text;

        var blockCap = document.createElement('div');
        blockCap.className = 'phrase-cap ng-non-bindable';
        var blockTime = document.createElement('div');
        blockTime.className = 'phrase-line1 fs-xs ng-non-bindable';
        var blockUser = document.createElement('div');
        blockUser.className = 'phrase-line2 fs-md ng-non-bindable';
        blockUser.setAttribute('onclick', 'selectTalkUsers(this)');
        var blockMessage = document.createElement('div');
        blockMessage.className = 'phrase-line3 fs-sm ng-non-bindable';

        blockCap.textContent = textUser.substring(0, 2);
        blockTime.textContent = textTime;
        blockUser.textContent = textUser;

        /* Find and attach links in user message. */
        var linkPattern = /\b(http|https):\/\/\S+/g;
        if(linkPattern.test(textMessage)) {
            var links = textMessage.match(linkPattern);
            subMessStart= 0;
            subMessEnd = textMessage.indexOf(links[0]);
            for(let index in links) {
                blockMessage.appendChild(document.createTextNode(textMessage.substring(subMessStart, subMessEnd)));
                
                var link = document.createElement('a');
                link.href = links[index];
                link.target = "_blank";
                link.rel = "noopener noreferrer"
                link.style.wordBreak = "break-all";
                link.textContent = links[index];
                blockMessage.appendChild(link);
                
                subMessStart = (subMessEnd + links[index].length);
                subMessEnd = subMessStart + (textMessage.substring(subMessStart, textMessage.length)).search(linkPattern);
            }
            blockMessage.appendChild(document.createTextNode(textMessage.substring(subMessStart, textMessage.length)));
        } else {
            blockMessage.textContent = textMessage;
        }

        var blockPhrase = document.createElement('div');
        blockPhrase.className = 'phrase-talk';

        if (lastMsg.user === nickname && lastMsg.time.hours ===  hours && lastMsg.time.minuts === minuts && !document.getElementById('form-talk').lastChild.classList.contains('days-ago-talk')) {
            blockCap.style.height = '0px';
            blockPhrase.appendChild(blockCap);
            blockPhrase.appendChild(blockMessage);
            document.getElementById('form-talk').lastChild.style.paddingBottom = "0";
            blockPhrase.style.padding = "0 0 8px";
        } else {
            const userColor = getUserColor(nickname);

            blockCap.style.background = userColor;
            blockUser.style.color = userColor;
            
            blockPhrase.appendChild(blockCap);
            blockPhrase.appendChild(blockTime);
            blockPhrase.appendChild(blockUser);
            blockPhrase.appendChild(blockMessage);
            lastMsg.user = nickname;
            lastMsg.time.hours = hours;
            lastMsg.time.minuts = minuts;
        }
        document.getElementById('form-talk').appendChild(blockPhrase);
        scrollToBottom("talk-content");
}

addToTalkSection = function(datatext) {
    var blockMessage = document.createElement('div');
    blockMessage.className ="days-ago-talk fs-md";
    blockMessage.textContent = datatext;

    document.getElementById('form-talk').appendChild(blockMessage);
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
var talkHeight = $('#talk-content').outerHeight();
$(window).resize(function() {
  if (talkWidth != $('#talk-content').outerWidth() || talkHeight != $('#talk-content').outerHeight()) {
    talkWidth = $('#talk-content').outerWidth();
    talkHeight = $('#talk-content').outerHeight();
    scrollToBottom("talk-content");
  }
});

downloadHistoryTalk();

/*#########################
--------- Logs -------
#########################*/

var searchPhrase = "", action = "", logsLimit = 40, logsOffset = 0;

function refreshLogs() {
    document.getElementById('logsSearch-input').value = '';
    document.getElementById('actionSelector').selectedIndex = 0;
    action = "";
    logsOffset = 0;
    getLogs();
}

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
        noMore.style.color = "var(--tc-secondary-low)";
        noMore.textContent = "No more Logs";
        noMore.id = 'noMoreLogs';
        document.getElementById('logsBox').append(noMore);
    } else {
        document.getElementById('nextLogs').style.display = "unset";
    }
}
const actionColors = {
    'rollback': '#c8b40e',
    'undo': '#db24b0',
    'delete': '#672dd2',
    'edit': '#2dd280',
    'warn': '#d92c26',
    'report': '#e3791c',
    'protect': '#1cb3e3'
}
function displayLogs (logs) {
    const logsCols = ['lt__sno', 'lt__user', 'lt__action', 'lt__wiki', 'lt__title', 'lt__date'];
    const logsColsName = ['SNo', 'User', 'Action', 'Wiki', 'Title', 'Date'];
    var sno = logsOffset;

    var logsTable = document.createElement('div');
    logsTable.id = 'logsTable';
    logsTable.className = 'logs-table';
    
    var headerRow = document.createElement('div');
    headerRow.className = 'lt-row fs-md';
    for (let i = 0; i < logsCols.length; i++) {
        var headerColumn = document.createElement('div');
        headerColumn.className = logsCols[i];
        headerColumn.textContent = logsColsName[i];
        headerRow.append(headerColumn);
    }
    logsTable.append(headerRow);

    logs.forEach((log) => {
        sno++;
        var columns = {};
        var row = document.createElement('div');
        row.className = 'lt-row fs-sm';
        logsCols.forEach((col) => {
            var column = document.createElement('div');
            column.className = col;
            columns[col] = column;
        })
        columns['lt__sno'].textContent = sno;
        var link = document.createElement('a');
        link.href = log['diff'].substring(0, (log['diff'].indexOf('.org/')) + 5) + "wiki/user:" + log['user'];
        link.textContent = log['user']; link.target = '_blank'; link.rel = "noopener noreferrer";
        columns['lt__user'].append(link);
        columns['lt__action'].textContent = log['type'];
        columns['lt__action'].style.color = actionColors[log['type']];
        columns['lt__wiki'].textContent = log['wiki'];
        var link = document.createElement('a');
        link.href = log['diff']; link.textContent = log['title']; link.target = '_blank'; link.rel = "noopener noreferrer";
        columns['lt__title'].append(link);
        columns['lt__date'].textContent = log['date'];

        for (column in columns) row.append(columns[column]);
        
        logsTable.append(row);
    });

    var logsBox = document.getElementById('logsBox');
    if (document.getElementById('noMoreLogs')) document.getElementById('noMoreLogs').remove();
    if (document.getElementById('logsTable')) document.getElementById('logsTable').remove();
    logsBox.append(logsTable);
    logsBox.parentElement.scrollTop = 0;
    handleLogsUI();
    logsBox.parentElement.classList.remove('disabled');
}
function getLogs(searchPhrase = "") {
    document.getElementById('logsBox').parentElement.classList.add('disabled');

    fetch('./php/logs.php', {
        method: 'post',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            sp: searchPhrase,
            st: action,
            li: logsLimit,
            of: logsOffset
        })
    }).then((response) => response.json())
    .then((logs) => displayLogs(logs))
    .catch((error) => console.error(error));
}

function searchLogs() {
    var searchInput = document.getElementById('logsSearch-input');
    logsOffset = 0;
    getLogs(searchInput.value);
}
getLogs();

/*#########################
--------- stat -------
#########################*/

function searchStat () {
    document.getElementById('statContainer').classList.add('disabled');
    var stats = ['rollback', 'delete', 'undo', 'edit', 'report', 'warn', 'protect'];
    stats.forEach((action) => document.getElementById(action + 'Span').textContent = '0');
    var searchPhrase = document.getElementById('statInput').value;
    fetch('./php/welcome-stats.php', {
        method: 'post',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ user: searchPhrase })
    }).then((response) => response.json())
    .then((stats) => {
        stats.forEach((action) => document.getElementById(action['type'] + 'Span').textContent = action['total']);
        document.getElementById('statContainer').classList.remove('disabled');
    }).catch((error) => console.error(error));
}
searchStat();

function toggleMoreWN (button) {
    if (document.getElementById('moreWN').style.height == '0px') {
        document.getElementById('moreWN').style.height = 'unset';
        button.textContent = 'Show less';
    } else {
        document.getElementById('moreWN').style.height = '0px';
        button.textContent = 'Show more';
    }
}

/*#########################
--------- onLoad -------
#########################*/

window.onload = function() {
    loadThemeList();
    if (settingslist['theme'] !== null && typeof settingslist['theme'] !== "undefined" && settingslist['theme'] !== "" && ( settingslist['theme'] >= 0 && settingslist['theme'] < (Object.keys(THEME)).length) ) {
        document.getElementById('themeSelector').selectedIndex = settingslist['theme'];
        changeTheme(parseInt(settingslist['theme']));
    } else changeTheme(0);
    document.getElementById('loading').style.display = "none";
    document.getElementById('app').style.display = "block";
};

</script>
<script src="js/swv.js"></script>
<script>uiDisableList();</script>
</body>
</html>