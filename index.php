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

        <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
        <meta name="application-name" content="SWViewer">
        <meta name="author" content="Iluvatar, Ajbura, 1997kB">
        <meta name="description" content="App for viewing queue of edits on small wikis for SWMT">
        <meta name="keywords" content="SWMT">
        <meta name="msapplication-TileColor" content="#808d9f">
        <!-- icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="img/favicons/favicon-16x16.png">
        <link rel="mask-icon" href="img/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <!-- Add iOS meta tags and icons -->
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="#191919">
        <meta name="apple-mobile-web-app-title" content="SWViewer">
        <link rel="apple-touch-icon" sizes="180x180" href="img/favicons/apple-touch-icon.png">
        <!-- PWA -->
        <meta name="theme-color" content="#191919">
        <link rel='manifest' href='manifest.webmanifest'>
        <script>
            if (window.navigator.userAgent.indexOf('MSIE ') > 0 || window.navigator.userAgent.indexOf('Trident/') > 0 || window.navigator.userAgent.indexOf('Edge/') > 0) {
                alert("Sorry, but Internet Explorer and Microsoft Edge browsers is not supported.");
                if (window.stop !== undefined) {
                    window.stop();
                } else if (document.execCommand !== undefined) {
                    document.execCommand("Stop", false);
                }
            }
        </script>
        <script async src="js/pwacompat.js"></script>
        
        <script>
            if ("serviceWorker" in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('./service-worker.js', {
                        scope: './'
                    })
                    .then((reg) => {
                        console.log('Service worker registered.', reg);
                    });
                });
            }
        </script>

        <!-- AngularJS, jQuery, Moment, pwacompat -->
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/angular.js/1.7.2/angular.min.js"></script>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/angular-ui/0.4.0/angular-ui.min.js"></script>
        
        
        <script type="text/javascript" src="./js/modules/bakeEl.min.js" defer></script>
        <script type="text/javascript" src="./js/modules/pw.js" defer></script>
        <script type="text/javascript" src="./js/modules/po.js" defer></script>

        <!-- Fonts, stylesheet-->
        <link rel="stylesheet" href="css/base/fonts.css">
        <link rel="stylesheet" href="css/base/variables.css">
        <link rel="stylesheet" href="css/base/base.css">
        <link rel="stylesheet" href="css/components/comp.css">
        <link rel="stylesheet" href="css/components/header.css">
        <link rel="stylesheet" href="css/components/dialog.css">
        <link rel="stylesheet" href="css/components/notification.css">
        <link rel="stylesheet" href="css/index.css?v=1.2">

        <link rel="stylesheet" href="css/components/pw-po.css">
        <link rel="stylesheet" href="css/layouts/logs.css">
        <link rel="stylesheet" href="css/layouts/talk.css">
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
$checkLoginSWV = true;
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_SESSION['userRole']) || !isset($_SESSION['mode']) || $_SESSION['mode'] == "" || !isset($_SESSION['talkToken']) || $_SESSION['talkToken'] == "") {
    $checkLoginSWV = false;

    if (isset($_COOKIE["SWViewer-auth"])) {
        $cookies = $_COOKIE["SWViewer-auth"];
        $obj = json_decode($cookies);
        if (!isset($obj->cookies)) {
            $_SESSION['userName'] = $obj->userName;
            $_SESSION['tokenKey'] = $obj->tokenKey;
            $_SESSION['tokenSecret'] = $obj->tokenSecret;
            $_SESSION['talkToken'] = $obj->talkToken;
            $_SESSION['userRole'] = $obj->userRole;
            $_SESSION['mode'] = $obj->mode;
            $_SESSION['accessGlobal'] = $obj->accessGlobal;
            $_SESSION['projects'] = $obj->projects;
        }
    }
}
if (isset($_SESSION['userName']) && !empty($_SESSION['userName']) && isset($_SESSION['tokenKey']) && !empty($_SESSION['tokenKey']) && isset($_SESSION['tokenSecret']) && !empty($_SESSION['tokenSecret']) && isset($_SESSION['talkToken']) && !empty($_SESSION['talkToken']) && $_SESSION['talkToken'] !== "" && isset($_SESSION['mode']) && !empty($_SESSION['mode']) && $_SESSION['mode'] !== null && $_SESSION['talkToken'] !== null && $_SESSION['mode'] !== "")
    $checkLoginSWV = true;

if ($checkLoginSWV == false) {
    session_write_close();
    echo "
        <noscript>
            <span style='color: red;'>JavaScript is not enabled!</span>
        </noscript>

        <div class='login-base secondary-cont'>
            <div class='login-card'>
                <div>
                    <span class='fs-xl' style='font-weight: bold;'>Welcome!</span>
                    <a id='abtn' class='i-btn__accent accent-hover' style='margin: 16px 0; color: var(--tc-accent) !important; padding: 0 24px; text-decoration: none !important;' href='https://swviewer.toolforge.org/php/oauth.php?action=auth'>OAuth Login</a>
                    <span class='fs-xs'>To use this application <a rel='noopener noreferrer' target='_blank' href='https://en.wikipedia.org/wiki/Wikipedia:Rollback'>local</a> or <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Global_rollback'>global</a> rollback is required.</span>
                    <span class='fs-xs' style='margin-top: 3px; width: 304.14px'>By clicking on the \"OAuth Login\" button, you agree to our <div style='display:inline; color: var(--link-color); text-decoration: none; cursor: pointer;' onclick='openPO();'>Cookie and Privacy policy</div>.</span>
                </div>
                <div>
                    <span class='i-btn__secondary-outlined secondary-hover fs-md' style='height: 35px; margin-bottom: 8px;' onclick='openPO();'>About</span>
                    <span class='fs-xs'>Brought to you by <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/User:Iluvatar'>Iluvatar</a>, <a rel='noopener noreferrer' target='_blank' href='https://ajbura.github.io'>ajbura</a>, <a rel='noopener noreferrer' target='_blank' href='https://en.wikipedia.org/wiki/User:1997kB'>1997kB</a></span>
                </div>    
            </div>
        </div>

        <!-- po Overlay-->
        <div id='POOverlay' class='po__overlay' onclick='closePO()'></div>

        <script>
            var lastOpenedPO = undefined;
            $.getScript('https://swviewer.toolforge.org/js/modules/about.js');
            function openPO (po = 'about') {
                function openPOLocal () {
                    document.getElementById(po).style.display = 'grid';
                    setTimeout(() => {
                        document.getElementById(po).classList.add('po__active');
                        document.getElementById('POOverlay').classList.add('po__overlay__active');
                    }, 0);
                    lastOpenedPO = po;
                }

                if (document.getElementById(po) === null) {
                    if (po === 'about') $.getScript('https://swviewer.toolforge.org/js/modules/about.js');

                    if (document.getElementById(po) !== null) openPOLocal();
                } else openPOLocal();
            }
            function closePO () {
                if (lastOpenedPO !== undefined) {
                    document.getElementById(lastOpenedPO).classList.remove('po__active');
                    document.getElementById('POOverlay').classList.remove('po__overlay__active');
                    setTimeout(() => {
                        document.getElementById(lastOpenedPO).style.display = 'none';
                    }, 200);
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

# User is not banned. Update date of last open (offline users in The Talk)
$q = $db->prepare('UPDATE user SET lastopen=CURRENT_TIMESTAMP WHERE name=:name');
$q->execute(array(':name' => $_SESSION["userName"]));

$userSelf = $_SESSION["userName"];
$isGlobalModeAccess = false;
$isGlobal = false;
if ($_SESSION['mode'] == "global")
    $isGlobal = true;
else
    if (isset($_SESSION['accessGlobal']))
        if ($_SESSION['accessGlobal'] === "true")
            $isGlobalModeAccess = true;
$userRole = $_SESSION['userRole'];
session_write_close();
?>

<script>
var xhr = new XMLHttpRequest();
xhr.open('POST', "php/getSessionVars.php", false);
xhr.send();
const sess = JSON.parse(xhr.responseText);
if (!sess.hasOwnProperty("user") || !sess.hasOwnProperty("isGlobal") || !sess.hasOwnProperty("isGlobalModeAccess") || !sess.hasOwnProperty("local_wikis") || !sess.hasOwnProperty("talktoken") || sess.hasOwnProperty("error")) {
    alert("Something gone wrong. Please retry.");
    xhr.open("GET", "php/oauth.php?action=unlogin", false);
    xhr.send();
    if (xhr.responseText == "Unlogin is done")
        window.open("https://swviewer.toolforge.org/", "_self");
}
const userSelf = sess["user"];
const userRole = sess["userRole"];
const isGlobal = Boolean(sess["isGlobal"]);
const isGlobalModeAccess = Boolean(sess["isGlobalModeAccess"]);
const talktoken = sess["talktoken"]; // DO NOT GIVE TO ANYONE THIS TOKEN, OTHERWISE THE ATTACKER WILL CAN OPERATE AND SENDS MESSAGES UNDER YOUR NAME!
var local_wikis = [];
if (sess["local_wikis"] !== "")
    local_wikis = sess["local_wikis"].split(',');
</script>

<body  class="full-screen" id="mainapp-body">

<!-- Loading UI -->
<div id="loading" class="secodnary-cont" style="padding: 16px; background: var(--bc-secondary); display: flex; align-items: center; justify-content: center; align-content: center; flex-wrap: wrap; position: fixed; z-index: 999;">
    <div style="width: 75px; height: 75px;">
        <svg version=1.1 id=Layer_1 xmlns=http://www.w3.org/2000/svg xmlns:xlink=http://www.w3.org/1999/xlink x=0px y=0px viewBox="0 0 512 512" enable-background="new 0 0 512 512" xml:space=preserve> <g id=sw-logo> <path id=base d="M255.9,503L255.9,503C119.3,503,8.5,392.3,8.5,255.6v0C8.5,119,119.3,8.2,255.9,8.2h0 c136.6,0,247.4,110.8,247.4,247.4v0C503.3,392.3,392.6,503,255.9,503z"/> <g id=diff> <path fill=#FFE49C d="M226.3,358.7l-69.2,18.6c-12,3.2-23.8-5.8-23.8-18.2v-207c0-12.4,11.8-21.5,23.8-18.2l69.2,18.6 c8.2,2.2,14,9.7,14,18.2v169.8C240.3,349,234.6,356.5,226.3,358.7z"/> <path fill=#D8ECFF d="M364.5,358.7l-69.2,18.6c-12,3.2-23.8-5.8-23.8-18.2v-207c0-12.4,11.8-21.5,23.8-18.2l69.2,18.6 c8.2,2.2,14,9.7,14,18.2v169.8C378.5,349,372.8,356.5,364.5,358.7z"/> </g> </g> </svg>
    </div>
    <h1 style="padding: 4px 16px 0">SWViewer
        <div id="loadingBar" style="height: 4px; width: 10%; background-color: #efefef; border-radius: 4px; transition: width 200ms ease-in;"></div>
    </h1>
</div>

<!-- Application UI -->
<div id="angularapp" ng-app="swv" ng-controller="Queue">
    <div class="base-container" id="app">
        <div id="baseGrid" class="base-grid">
            <!-- sidebar -->
            <div id="sidebar" class="sidebar-base primary-cont">
                <div class="sidebar__options">
                    <div id="btn-home" class="tab__active primary-hover" onclick="clickHome(); closePW();" aria-label="SWViewer [esc]" i-tooltip="right">
                        <div class="tab-indicator"></div>
                        <img class="touch-ic primary-icon" src="./img/swviewer-filled.svg" alt="SWViewer image">
                    </div>
                    <div id="btn-talk" class="primary-hover disabled" onclick="openPW('talkForm')" aria-label="Talk [t]" i-tooltip="right">
                        <div class="tab-indicator"></div>
                        <span id="badge-talk" class="tab-notice-indicator" style="background-color: var(--tc-primary);">{{users.length}}</span>
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon" src="./img/message-filled.svg" alt="Message image">
                    </div>
                    <div id="btn-logs" class="primary-hover" onclick="openPW('logs')" aria-label="Logs [l]" i-tooltip="right">
                        <div class="tab-indicator"></div>
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon" src="./img/doc-filled.svg" alt="Logs image">
                    </div>
                    <div id="btn-unlogin" class="primary-hover" onclick="logout(); closeSidebar();" aria-label="Logout [u]" i-tooltip="right">
                        <img class="touch-ic primary-icon" src="./img/power-filled.svg" alt="Logout image">
                    </div>
                    <div id="btn-about" class="primary-hover" style="margin-top: auto;" onclick="openPO('about'); closeSidebar();" aria-label="About" i-tooltip="right">
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon" src="./img/about-filled.svg" alt="About image">
                    </div>
                    <div id="btn-notification" class="primary-hover" onclick="openPO('notificationPanel'); closeSidebar();" aria-label="Notifications [n]" i-tooltip="right">
                        <span id="notify-indicator" class="tab-notice-indicator tab-notice-indicator__inactive" style="background-color: var(--bc-negative);">0</span>
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon" src="./img/bell-filled.svg" alt="Notification image">
                    </div>
                    <div id="btn-settings" class="primary-hover" onclick="openPO('settingsOverlay'); closeSidebar();" aria-label="Settings and quick links [s]" i-tooltip="right">
                        <img class="touch-ic primary-icon" src="./img/settings-filled.svg" alt="Settings image">
                    </div>
                </div>
            </div>
            <!-- Drawer -->
            <div id="queueDrawer" class="drawer-base primary-cont">
                <div class="edit-queue-base">
                    <div class="action-header eq__header">
                        <div class="mobile-only primary-hover" onclick="openSidebar();" aria-label="Sidebar" i-tooltip="bottom-left">
                            <img class="touch-ic primary-icon" src="./img/drawer-filled.svg" alt="Navigation image">
                        </div>
                        <span id="presetsArrow" class="presets-arrow action-header__title fs-lg disabled" onClick="togglePresets()">
                            <span id="drawerPresetTitle" class="drawer-preset-title" >Default</span>
                        </span>
                        <div id="editCurrentPreset" class="primary-hover disabled" aria-label="Edit" i-tooltip="bottom-right">
                            <img class="touch-ic primary-icon" src="./img/pencil-filled.svg" alt="Edit image">
                        </div>
                        <div id="moreOptionBtnMobile" class="mobile-only primary-hover disabled" onclick="toggleMoreControl();" aria-label="More options" i-tooltip="bottom-right">
                            <img class="touch-ic primary-icon" src="./img/v-dots-filled.svg" alt="More option img">
                        </div>
                    </div>
                    <div id="presetBody" class="preset__body" style="height: 0;">
                        <div class="primary-scroll">
                            <div id="presetsBase" class="fs-md">
                                <button class="i-btn__primary primary-hover fs-sm" style="background-color: var(--bc-primary-hover);" onclick="editPreset();">
                                    <img class="touch-ic primary-icon" src="./img/plus-filled.svg" alt="Plus image">Create
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="eqBody" class="eq__body">
                        <div class="queue-base primary-scroll">
                            <div class="queue" id="queue">
                                <div class="primary-hover"  ng-click="select(edit)" ng-repeat="edit in edits track by $index">
                                    <div class="queue-col">
                                        <div class="queue-ores" style="background-color: {{edit.ores.color}}">{{edit.ores.score}}</div>
                                        <div class="queue-new">{{edit.isNew}}</div>
                                    </div>
                                    <div class="queue-row">
                                        <div class="queue-wikiname fs-sm" ng-style="editColor(edit)">{{edit.wiki}}</div>
                                        <div class="queue-username fs-xs">{{edit.user}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Status Bar -->
            <div class="statusbar-base"></div>
            <!-- Main Window -->
            <div class="window-base secondary-cont">
                <div id="windowContent" class="window-content">
                    <!-- description container -->
                    <div id="description-container" class="description-container fs-md" style="display: none; margin-top: 0;">
                        <div class="desc-un">
                            <div id="us" class="fs-sm">User: <div id="userLinkSpec" ng-click="openLink('diff');"></div></div>
                            <div id="ns" class="fs-sm"></div>
                        </div>
                        <div class="desc-wt">
                            <div id="wiki" class="fs-sm"></div>
                            <div id="tit" class="fs-sm" style="overflow: unset">Title: <div id="pageLinkSpec" style="cursor: pointer; display: inline-block; color: var(--link-color);" ng-click="openLink('page');"></div></div>
                        </div>
                        <div class="desc-c">
                            <div id="com" class="fs-sm"></div>
                        </div>
                    </div>
                    <!-- Mobile next diff button -->
                    <div id="drawerFab" class="drawer-fab mobile-only">
                        <div id="next-diff" class="accent-hover" ng-click='nextDiff()' aria-label="Next difference" i-tooltip="top-right">
                            <img class="touch-ic accent-icon" src="./img/swviewer-filled.svg" alt="Next diffrence image">
                        </div>
                        <span id="next-diff-title" class="fs-md">Fetching</span>
                        <div class="accent-hover" style="position: relative;" onclick="toggleMDrawer();" aria-label="Queue" i-tooltip="top-right">
                            <span class="drawer-btn__edits-count">{{edits.length}}</span>
                            <img class="touch-ic accent-icon" src="./img/drawer-filled.svg" alt="Drawer image">
                        </div>
                    </div>
                    <div id="notificationFabBase" class="notification-fab-base notification-fab-base__inactive drawer-fab mobile-only">
                        <div id="notificationFab" class="secondary-hover" onclick="openPO('notificationPanel');" aria-label="Notifications" i-tooltip="top-left">
                            <span id="notify-fab-indicator" class="tab-notice-indicator" style="background-color: var(--bc-negative);">0</span>
                            <img class="secondary-icon touch-ic" src="/img/bell-filled.svg" alt="Bell image">
                        </div>
                    </div>
                    <!-- Controls -->
                    <div id="moreControlOverlay" class="more-control__overlay"  onclick="closeMoreControl();"></div>
                    <div id="controlsBase" class="controls-base floatbar"  style="display: none;">
                        <!-- More control -->
                        <div id="moreControl" class="more-control more-control__hidden secondary-scroll">
                            <div>
                                <a class="secondary-hover fs-sm" href='https://meta.wikimedia.org/wiki/Meta:Requests_for_help_from_a_sysop_or_bureaucrat' rel='noopener noreferrer' target='_blank'>Meta:RFH</a>
                                <span vr-line="secondary"></span>
                                <a class="secondary-hover fs-sm" href='https://meta.wikimedia.org/wiki/Steward_requests/Miscellaneous' rel='noopener noreferrer' target='_blank'>SRM</a>
                                <span vr-line="secondary"></span>
                                <a class="secondary-hover fs-sm" href='https://meta.wikimedia.org/wiki/Steward_requests/Global' rel='noopener noreferrer' target='_blank'>SRG</a>
                                <span vr-line="secondary"></span>
                                <a class="secondary-hover fs-sm" href='https://meta.wikimedia.org/wiki/Global_sysops/Requests' rel='noopener noreferrer' target='_blank'>GSR</a>
                            </div>
                            <div id="CAUTH">
                                <div class="secondary-hover" ng-click="copyCentralAuth()" aria-label="Copy link address" i-tooltip="top-left"><img class="touch-ic secondary-icon" src="./img/copy-filled.svg" alt="Copy Image"></div>
                                <a class="secondary-hover fs-md" href='https://meta.wikimedia.org/wiki/Special:CentralAuth?target={{selectedEdit.user}}' onclick="toggleMoreControl();" rel='noopener noreferrer' target='_blank'>Central auth</a>
                            </div>
                            <div>
                                <div class="secondary-hover" ng-click="copyGlobalContribs()" aria-label="Copy link address" i-tooltip="top-left"><img class="touch-ic secondary-icon" src="./img/copy-filled.svg" alt="Copy Image"></div>
                                <a class="secondary-hover fs-md" href='https://guc.toolforge.org/?src=hr&by=date&user={{selectedEdit.user}}' onclick="toggleMoreControl();" rel='noopener noreferrer' target='_blank'>Global contribs</a>
                            </div>
                            <div>
                                <div class="secondary-hover" ng-click="copyViewHistory()" aria-label="Copy link address" i-tooltip="top-left"><img class="touch-ic secondary-icon" src="./img/copy-filled.svg" alt="Copy Image"></div>
                                <a class="secondary-hover fs-md" href='{{selectedEdit.server_url + "" + selectedEdit.script_path}}/index.php?title={{selectedEdit.title}}&action=history' onclick="toggleMoreControl();" rel='noopener noreferrer' target='_blank'>View history</a>
                            </div>
                            <div >
                                <div id="editBtn" class="secondary-hover" ng-click="openEditSource();" onclick="openPW('editForm'); closeMoreControl();" aria-label="Edit source [e]" i-tooltip="top-left">
                                    <img class="touch-ic secondary-icon" src="./img/edit-filled.svg" alt="Edit img">
                                </div>
                                <a class="secondary-hover fs-md" ng-click="openEditSource();" onclick="openPW('editForm'); closeMoreControl();"><span style="color: var(--tc-secondary);">Edit Source</span></a>
                            </div>
                        </div>
                        <!-- Control buttons -->
                        <div id="control" class="toolbar">
                            <div class="desktop-only secondary-hover" onclick="toggleMoreControl();" aria-label="More options" i-tooltip="top-right">
                                <img class="touch-ic secondary-icon" src="./img/v-dots-filled.svg" alt="More option img">
                            </div>
                            <div id="browser" class="secondary-hover" ng-click="browser();" aria-label="Open in browser window [o]" i-tooltip="top-right">
                                <img class="touch-ic secondary-icon" src="./img/open-newtab-filled.svg" alt="Open in new tab img">
                            </div>
                            <div id="tagBtn" class="secondary-hover" ng-click="openTagPanel();" onclick="openPW('tagPanel')" aria-label="Tag panel [d]" i-tooltip="top">
                                <img class="touch-ic secondary-icon" src="./img/tag-filled.svg" alt="Edit img">
                            </div>
                            <div id="customRevertBtn" class="secondary-hover" ng-click="openCustomRevertPanel();" aria-label="Rollback with summary [y]" i-tooltip="top">
                                <img class="touch-ic secondary-icon" src="./img/custom-rollback-filled.svg" alt="Custom rollback img">
                            </div>
                            <div id="revert" class="secondary-hover" ng-click="doRevert();" aria-label="Quick Rollback [r]" i-tooltip="top">
                                <img class="touch-ic secondary-icon" src="./img/rollback-filled.svg" alt="Rollback img">
                            </div>
                            <div id="back" class="secondary-hover" ng-click="Back();" aria-label="Previous diff [Left square bracket or p]" i-tooltip="top-left">
                                <img class="touch-ic secondary-icon" src="./img/arrow-left-filled.svg" alt="back image">
                            </div>
                        </div>
                    </div>
                    <!-- Welcome page and Difference viewer -->
                    <div class="diff-container frame-diff">
                        <iframe id='page-welcome' class='full-screen' style='display: block;' title='Welcome page' src='templates/welcome.html'></iframe>
                        <iframe id='page' class='full-screen' style='display: none;' title='Diff' sandbox='allow-same-origin allow-scripts'></iframe>
                    </div>

                    <!-- Edit Source | popup-window -->
                    <div id="editForm" class="pw__base" style='display: none; grid-template-areas: "pw__header pw__header" "pw__content pw__content";'>
                        <!--pw Header-->
                        <div class="pw__header action-header">
                            <div class="mobile-only secondary-hover" onclick="openSidebar();" aria-label="Sidebar" i-tooltip="bottom-left">
                                <img class="touch-ic secondary-icon" src="./img/drawer-filled.svg" alt="Box Image">
                            </div>
                            <span class="action-header__title fs-xl">Edit Source</span>
                            <div class="mobile-only secondary-hover" onclick="closePW()" aria-label="Close [esc]" i-tooltip="bottom-right">
                                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
                            </div>
                            <span class="desktop-only pw__esc secondary-hover fs-md" onclick="closePW()">esc</span>
                        </div>
                        <!--pw Content-->
                        <div id="editFormBody" class="pw__content">
                            <img id="editSourceLoadingAnim" class="secondary-icon touch-ic" src="/img/swviewer-droping-anim.svg" style="opacity: .4; width: 100px; height: 100px; margin: auto;">
                            <textarea id="textpage" class="pw__content-body secondary-scroll editForm__textarea fs-md" style="padding-bottom: 40px;" title="Source code of page"></textarea>

                            <div class="pw__floatbar">
                                <form ng-submit="saveEdit()"><input id="summaryedit" class="secondary-placeholder fs-md" title="Summary" placeholder="Briefly describe your changes."></form>
                                <span vr-line></span>
                                <div id="editForm-save" class="secondary-hover" ng-click="saveEdit()" aria-label="Publish changes" i-tooltip="top-right">
                                    <img class="touch-ic secondary-icon" src="./img/save-filled.svg" alt="Save image">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- floating overlay --> 
                    <div id="floatingOverlay" class="floating-overlay" onclick="closeSidebar();"></div>
                </div>
            </div>
        </div>
    </div>

    <script>document.getElementById('loadingBar').style.width = "30%";</script>
    <!-- customRevert | Popup-overlay -->
    <div id="customRevert" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">Custom revert</span>
            <div class="mobile-only secondary-hover" onclick="closePO()" aria-label="Close [esc]" i-tooltip="bottom-right">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <form id="summariesContainer" style="display: flex" ng-submit="doRevert();">
                    <input class="i-input__secondary secondary-placeholder fs-md" style="margin-right: 8px;" title="Reason" name="credit" id="credit" placeholder="Provide a reason."/>
                    <button type="button" class="i-btn__accent accent-hover fs-md" id="btn-cr-u-apply" ng-click="doRevert();">Revert</button>
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
                <div class="panel-cr-reasons" ng-repeat="description in selectedEdit.config.rollback track by $index">
                    <div class="fs-sm" ng-style="descriptionColor(description)" ng-click="selectRollbackDescription(description)">{{description.name}}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- tagPanel | Popup-overlay -->
    <div id="tagPanel" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">Tag for deletion</span>
            <div class="mobile-only secondary-hover" onclick="closePO()" aria-label="Close [esc]" i-tooltip="bottom-right">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <div class="i__base">
                    <div class="i__title fs-md">Warn user</div>
                    <div class="i__description fs-xs">Turning this on will left a notification on the user talk page after clicking templates (green only).</div>
                    <div class="i__content fs-sm">
                        <div id="warn-box-delete" class="t-btn__secondary"></div>
                    </div>
                </div>
                <div id="speedyReasonsBox">
                    <div class="panel-cr-reasons" ng-repeat="speedy in selectedEdit.config.speedy track by $index" onclick="closePO();">
                        <div class="fs-sm" ng-style="speedyColor(speedy)" ng-click="selectSpeedy(speedy)">{{speedy.name}}</div>
                    </div>
                </div>
                <br/>
                <div id="btn-group-addToGSR" class="i__base">
                    <?php if ($userRole == "none") echo '<div class="i__title fs-md">Add to GSR</div>'; ?>
                    <div id="addToGSR-description" class="i__description fs-xs"></div>
                    <div class="i__content fs-sm" <?php if ($userRole !== "none") echo 'style="display: none"'; ?> >
                        <span id="addToGSR" class="i-checkbox" onclick="toggleICheckBox (this);"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Settings | Popup-overlay -->
    <div id="settingsOverlay" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg">Settings</span>
            <div class="mobile-only secondary-hover" onclick="closePO()" aria-label="Close [esc]" i-tooltip="bottom-right">
                <img class="touch-ic secondary-icon" src="./img/cross-filled.svg" alt="Cross image">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <div id="settingsBase">
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
                        <div class="i__title fs-md">Revisions</div>
                        <div class="i__description fs-xs">Set alert or open all consecutive revisions by same user at once (Only last is fastest).</div>
                        <div class="i__content fs-sm">
                            <select id="checkSelector" class="i-select__secondary fs-md">
                                <option value="0">Only last</option>
                                <option value="1">Alert on revert</option>
                                <option value="2">Show all</option>
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
                    <div class="i__base">
                        <div class="i__title fs-md">Terminate stream</div>
                        <div class="i__description fs-xs">Terminate recent changes stream when queue limit reaches. Big data saving.</div>
                        <div class="i__content fs-sm">
                            <div id="terminate-stream-btn" class="t-btn__secondary" onclick="toggleTButton(this); terminateStreamBtn(this, false);"></div>
                        </div>
                    </div>
                    <div class="i__base">
                        <div class="i__title fs-md">Queue limit</div>
                        <div class="i__description fs-xs">Max count of edits allowed to load in queue.</div>
                        <div class="i__content fs-sm">
                            <input id="max-queue" class="i-input__secondary secondary-placeholder fs-sm" name="max-queue" placeholder="No limit">
                        </div>
                    </div>
                    <div class="action-header">
                        <span class="action-header__title fs-lg" style="padding-left: 0;">Quick links</span>
                    </div>
                    <div class="i__base">
                        <div class="i__title fs-md">Scripts, templates</div>
                        <div class="i__extra">
                            <ul class="i-chip-list fs-sm">
                                <li><a class="fs-sm" href='https://meta.wikimedia.org/wiki/User:Hoo_man/Scripts/Tagger' rel='noopener noreferrer' target='_blank'>Tagger</a></li>
                                <li><a class="fs-sm" href='https://meta.wikimedia.org/wiki/User:Syum90/Warning_templates' rel='noopener noreferrer' target='_blank'>Warnings</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="i__base">
                        <div class="i__title fs-md">Translators</div>
                        <div class="i__extra">
                            <ul class="i-chip-list fs-sm">
                                <li><a class="fs-sm" href='https://translate.google.com/#auto/en/' rel='noopener noreferrer' target='_blank'>Google</a></li>
                                <li><a class="fs-sm" href='https://translate.yandex.com/' rel='noopener noreferrer' target='_blank'>Yandex</a></li>
                                <li><a class="fs-sm" href='http://www.online-translator.com' rel='noopener noreferrer' target='_blank'>Promt</a></li>
                                <li><a class="fs-sm" href='https://www.bing.com/translator' rel='noopener noreferrer' target='_blank'>Bing</a></li>
                                <li><a class="fs-sm" href='https://www.deepl.com/en/translator' rel='noopener noreferrer' target='_blank'>DeepL</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="i__base">
                        <div class="i__title fs-md">Contact</div>
                        <div class="i__extra">
                            <ul class="i-chip-list fs-sm">
                                <li><a class="fs-sm" href='http://ircredirect.toolforge.org/?server=irc.freenode.net&channel=swviewer&consent=yes' rel='noopener noreferrer' target='_blank'>IRC</a></li>
                                <li><a class="fs-sm" href='https://discord.gg/UTScYTR' rel='noopener noreferrer' target='_blank'>Discord</a></li>
                            </ul>
                        </div>
                    </div>
                    <?php if ($userSelf == "Ajbura" || $userSelf == "Iluvatar" || $userSelf == "1997kB") {
                        echo '
                            <div class="i__base">
                                <div class="i__title fs-md">Control SWV</div>
                                <div class="i__extra">
                                    <ul class="i-chip-list fs-sm">
                                        <li><a id="cpLink" class="fs-sm" href="https://swviewer.toolforge.org/php/control.php" rel="noopener noreferrer" target="_blank">Control panel</a></li>
                                    </ul>
                                </div>
                            </div>
                        ';
                    }?>
                </div>
            </div>
        </div>
    </div>

    <!-- po Overlay-->
    <div id="POOverlay" class="po__overlay" onclick="closePO()"></div>

    <!-- Edit preset | Template -->
    <template id="editPTitleTemplate">
        <div>
            <span class="fs-sm">Title</span>
            <input id="presetTitleInput" class="i-input__secondary secondary-placeholder fs-md" type="text" autocomplete="off" placeholder="">
        </div><br/>
    </template>
    <template id="editPresetTemplate">
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
                <div id="btn-delete-ns" class="i-minus fs-sm" onclick="nsDeleteFunct()">-</div>
                <input id="ns-input" class="i-input__secondary secondary-placeholder fs-sm" name="" placeholder="Enter">
                <div id="btn-add-ns" class="i-plus fs-sm" onclick="nsAddFunct()">+</div>
            </div>
            <div class="i__extra">
                <ul id="nsList" class="i-chip-list fs-sm"></ul>
            </div>
        </div>
        
        <?php if ($isGlobal == true || $isGlobalModeAccess === true) { echo '
            <div class="i__base">
                <div class="i__title fs-md">Small wikis</div>
                <div class="i__description fs-xs">Enable edits from small wikis.</div>
                <div class="i__content fs-sm">
                    <div id="small-wikis-btn" class="t-btn__secondary" onclick="toggleTButton(this); smallWikisBtn(this);"></div>
                </div>
            </div>
            <div class="i__base">
                <div class="i__title fs-md">Additional wikis</div>
                <div class="i__description fs-xs">Enable edits from <a style="display: inline;" href="https://meta.wikimedia.org/wiki/SWViewer/wikis" rel="noopener noreferrer" target="_blank">wikis</a> with less then 300 active users.</div>
                <div class="i__content fs-sm">
                    <div id="lt-300-btn" class="t-btn__secondary" onclick="toggleTButton(this); lt300Btn(this);"></div>
                </div>
            </div>
            <div class="i__base">
                <div class="i__title fs-md">Custom wikis</div>
                <div class="i__description fs-xs">Add your home-wiki or wikis which are not in small wikis list. Example: enwiki</div>
                <div class="i__content fs-sm">
                    <div id="btn-bl-p-delete" class="i-minus fs-sm" onclick="blpDeleteFunct()">-</div>
                    <input id="bl-p" class="i-input__secondary secondary-placeholder fs-sm" name="bl-p" placeholder="Enter">
                    <div id="btn-bl-p-add" class="i-plus fs-sm" onclick="blpAddFunct()">+</div>
                </div>
                <div class="i__extra">
                    <ul id="blareap" class="i-chip-list fs-sm"></ul>
                </div>
            </div>
        ';}?>

        <div class="i__base">
            <div class="i__title fs-md">Wikis whitelist</div>
            <div class="i__description fs-xs">Add wikis to skip their edits from queue. Example: enwiki</div>
            <div class="i__content fs-sm">
                <div id="btn-wl-p-delete" class="i-minus fs-sm" onclick="wluDeleteFunct()">-</div>
                <input id="wladdp" class="i-input__secondary secondary-placeholder fs-sm" name="wladdp" placeholder="Enter">
                <div id="btn-wl-p-add" class="i-plus fs-sm" onclick="wlpAddFunct()">+</div>
            </div>
            <div class="i__extra">
                <ul id="wlareap" class="i-chip-list fs-sm"></ul>
            </div>
        </div>
        <div class="i__base">
            <div class="i__title fs-md">Users whitelist</div>
            <div class="i__description fs-xs">Add users to skip their edits from queue. Example: JohnDoe</div>
            <div class="i__content fs-sm">
                <div id="btn-wl-u-delete" class="i-minus fs-sm" onclick="wluDeleteFunct()">-</div>
                <input id="wladdu" class="i-input__secondary secondary-placeholder fs-sm" name="wladdu" placeholder="Enter">
                <div id="btn-wl-u-add" class="i-plus fs-sm" onclick="wluAddFunct()">+</div>
            </div>
            <div class="i__extra">
                <ul id="wlareau" class="i-chip-list fs-sm"></ul>
            </div>
        </div>


    </template>

</div>

<script src="js/index-noncritical.js" defer></script>
<script src="js/modules/dialog.js" defer></script>
<script src="js/modules/presets.js" defer></script>
<script src="js/modules/swipe.js" defer></script>

<!-- Scripts -->
<script>
document.getElementById('loadingBar').style.width = "50%";
var diffstart, diffend, newstart, newend, startstring, endstring, config;
var global = [];
var activeSysops = [];
var vandals = [];
var suspects = [];
var sandboxlist = {};
var offlineUsers = [];
var defaultWarnList = [];
var defaultDeleteList = [];
var nsList = { 0: "Main", 1: "Talk", 2: "User", 3: "User talk", 4: "Project", 5: "Project talk", 6: "File", 7: "File talk", 10: "Template", 11: "Template talk", 12: "Help", 13: "Help talk", 14: "Category", 15: "Category talk", 100: "Portal", 101: "Portal talk", 108: "Book", 109: "Book talk", 118: "Draft", 119: "Draft talk", 446: "Education program", 447: "Education program talk", 710: "TimedText", 711: "TimedText talk", 828: "Module", 828: "Module talk"};
var countqueue = 0;
var regdays = 5;
var checkMode = 0;
var countedits = 100;
var sound = 0;
var newSound;
var terminateStream = 0;
var messageSound;
var privateMessageSound;
var firstClick = false;
var firstClickEdit = false;
var preSettings = {};
// presets value here is temp until we refill it from database.
var presets = [{ title: "", regdays: "5", editscount: "100", anons: "1", registered: "1", new: "1", onlynew: "0", swmt: "0", users: "0", namespaces: "", wlusers: "", wlprojects: "", blprojects: ""}];
var selectedPreset = 0;
var themeIndex = undefined;
const R_HSL = {
    h: (Math.floor(Math.random() * 361)),
    s: (Math.floor(Math.random() * 30) + 0),
    l: (Math.floor(Math.random() * 12) + 0)
};
const THEME_FIX = { '--bc-positive': 'rgb(36, 164, 100)', '--bc-negative': 'rgb(251, 47, 47)', '--ic-accent': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)', '--tc-accent': 'rgba(255, 255, 255, 1)', '--link-color': '#337ab7', '--tc-positive': 'var(--bc-positive)', '--tc-negative': 'var(--bc-negative)', '--fs-xl': '26px', '--fs-lg': '18px', '--fs-md': '16px', '--fs-sm': '14px', '--fs-xs': '11px', '--lh-xl': '1.125', '--lh-lg': '1.25', '--lh-md': '1.5', '--lh-sm': '1.5', '--lh-xs': '1.5', };
const BC_LIGHT = { '--bc-secondary': '#ffffff', '--bc-secondary-low': '#f4f4f4', '--bc-secondary-hover': 'rgba(0, 0, 0, .1)', };
const TCP_ON_DARK = { '--tc-primary': 'rgba(255, 255, 255, 1)', '--tc-primary-low': 'rgba(255, 255, 255, .8)', };
const TCP_ON_LIGHT = { '--tc-primary': 'rgba(0, 0, 0, 1)', '--tc-primary-low': 'rgba(0, 0, 0, .7)', };
const TCS_ON_LIGHT = { '--tc-secondary': 'rgba(0, 0, 0, 1)', '--tc-secondary-low': 'rgba(0, 0, 0, .7)', };
const TCS_ON_DARK = { '--tc-secondary': 'rgba(255, 255, 255, 1)', '--tc-secondary-low': 'rgba(255, 255, 255, .8)', };
const BCA_LIGHT = { '--bc-accent': '#0063E4', '--bc-accent-hover': '#0056C7', };
const BCA_DARK = { '--bc-accent': '#0050b8', '--bc-accent-hover': '#003c8a', };
const ICP_ON_DARK = { '--ic-primary': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)', };
const ICP_ON_LIGHT = { '--ic-primary': 'invert(0.30) sepia(1) saturate(0) hue-rotate(200deg)', };
const ICS_ON_LIGHT = { '--ic-secondary': 'invert(0.30) sepia(1) saturate(0) hue-rotate(200deg)', };
const ICS_ON_DARK = { '--ic-secondary': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)', };
const THEME = {
    "Default": { '--bc-primary': '#191919', '--bc-primary-low': '#212121', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        ...BC_LIGHT, ...ICP_ON_DARK, ...ICS_ON_LIGHT, ...BCA_LIGHT, ...TCP_ON_DARK, ...TCS_ON_LIGHT, ...THEME_FIX },
    "Light": { '--bc-primary': '#e8e8e8', '--bc-primary-low': '#f6f6f6', '--bc-primary-hover': 'rgba(0, 0, 0, .1)',
        ...BC_LIGHT, ...ICP_ON_LIGHT, ...ICS_ON_LIGHT, ...BCA_LIGHT, ...TCP_ON_LIGHT, ...TCS_ON_LIGHT,...THEME_FIX },
    "Dark": { '--bc-primary': '#0f1115', '--bc-primary-low': '#15171d', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-secondary': '#1c1e26', '--bc-secondary-low': '#21242c', '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
        ...ICP_ON_DARK, ...ICS_ON_DARK, ...BCA_DARK, ...TCP_ON_DARK, ...TCS_ON_DARK, ...THEME_FIX },
    "AMOLED": { '--bc-primary': '#000000', '--bc-primary-low': '#050505', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-secondary': '#000000', '--bc-secondary-low': '#111111', '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
        ...ICP_ON_DARK, ...ICS_ON_DARK, ...BCA_DARK, ...TCP_ON_DARK, ...TCS_ON_DARK, ...THEME_FIX },
    "Random": { '--bc-primary': `hsl(${R_HSL.h}, ${R_HSL.s}%, ${R_HSL.l}%)`, '--bc-primary-low': `hsl(${R_HSL.h}, ${R_HSL.s}%, ${R_HSL.l + 5}%)`, '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
        '--bc-secondary': `hsl(${R_HSL.h}, ${R_HSL.s}%, ${R_HSL.l + 8}%)`, '--bc-secondary-low': `hsl(${R_HSL.h}, ${R_HSL.s}%, ${R_HSL.l + 10}%)`, '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
        ...ICP_ON_DARK, ...ICS_ON_DARK, ...BCA_DARK, ...TCP_ON_DARK, ...TCS_ON_DARK, ...THEME_FIX },
};

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
addSandbox(sandboxlist, "wikidatawiki", "Q4115189");
addSandbox(sandboxlist, "wikidatawiki", "Q13406268");
addSandbox(sandboxlist, "wikidatawiki", "Q15397819");
addSandbox(sandboxlist, "wikidatawiki", "Property:P368");
addSandbox(sandboxlist, "wikidatawiki", "Property:P369");
addSandbox(sandboxlist, "wikidatawiki", "Property:P370");
addSandbox(sandboxlist, "wikidatawiki", "Property:P578");
addSandbox(sandboxlist, "wikidatawiki", "Property:P626");
addSandbox(sandboxlist, "wikidatawiki", "Property:P855");
addSandbox(sandboxlist, "wikidatawiki", "Property:P1106");
addSandbox(sandboxlist, "wikidatawiki", "Property:P1450");
addSandbox(sandboxlist, "wikidatawiki", "Property:P2368");
addSandbox(sandboxlist, "wikidatawiki", "Property:P2535");
addSandbox(sandboxlist, "wikidatawiki", "Property:P2536");
addSandbox(sandboxlist, "wikidatawiki", "Property:P4047");
addSandbox(sandboxlist, "wikidatawiki", "Property:P5188");
addSandbox(sandboxlist, "wikidatawiki", "Property:P5189");

function getPresets(setList) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', "php/presets.php?action=get_presets", false);
    xhr.send();
    presets = JSON.parse(xhr.responseText);
    presets.forEach(function(el, index) {
        if (el["title"] === setList["preset"])
            selectedPreset = index;
        if (el["namespaces"] === null) presets[index]["namespaces"] = "";
        if (el["blprojects"] === null) presets[index]["blprojects"] = "";
        if (el["wlprojects"] === null) presets[index]["wlprojects"] = "";
        if (el["wlusers"] === null) presets[index]["wlusers"] = "";
    });
    document.getElementById('presetsArrow').classList.remove('disabled');
    document.getElementById('editCurrentPreset').classList.remove('disabled');
}


xhr.open('GET', "php/settings.php?action=get&query=all", false);
xhr.send();
// Bug with session on Safari browser
if (xhr.responseText == "Invalid request")
    location.reload();
var settingslist  = xhr.responseText;
settingslist = JSON.parse(settingslist);

if (settingslist['theme'] !== null && typeof settingslist['theme'] !== "undefined" && settingslist['theme'] !== "" && ( settingslist['theme'] >= 0 && settingslist['theme'] < (Object.keys(THEME)).length) ) {
    themeIndex = parseInt(settingslist['theme']);
}

if (settingslist['checkmode'] !== null && (typeof settingslist['checkmode'] !== "undefined") && settingslist['checkmode'] !== "") {
    if (settingslist['checkmode'] === "1" || settingslist['checkmode'] === "2" || settingslist['checkmode'] === "0") {
        checkMode = Number(settingslist['checkmode']);
        document.getElementById("checkSelector").value = checkMode;
    }
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
    }
}
if (settingslist['terminateStream'] !== null && (typeof settingslist['terminateStream'] !== "undefined") && settingslist['terminateStream'] !== "") {
    if (settingslist['terminateStream'] === "1") {
        toggleTButton(document.getElementById("terminate-stream-btn"));
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

if (settingslist['countqueue'] !== null && (typeof settingslist['countqueue'] !== "undefined") && settingslist['countqueue'] !== "" && settingslist['countqueue'] !== "0") {
    countqueue = settingslist['countqueue'];
    document.getElementById("max-queue").value = countqueue;
}

if (settingslist['defaultdelete'] !== null && (typeof settingslist['defaultdelete'] !== "undefined") && settingslist['defaultdelete'] !== "") {
    defaultDeleteList = settingslist['defaultdelete'].split(',');
}

if (settingslist['defaultwarn'] !== null && (typeof settingslist['defaultwarn'] !== "undefined") && settingslist['defaultwarn'] !== "") {
    defaultWarnList = settingslist['defaultwarn'].split(',');
}

function loadDiffTemp(url, callback) {
    $.ajax({ type: 'POST', url: url, dataType: 'text',
        success: text => callback(text)
    })
}
loadDiffTemp('templates/diffStart.html', (text) =>  diffstart = setStrTheme(text, getStrTheme(THEME[Object.keys(THEME)[themeIndex]])) );
loadDiffTemp('templates/diffEnd.html', text => diffend = text );
loadDiffTemp('templates/newStart.html', text => newstart = setStrTheme(text, getStrTheme(THEME[Object.keys(THEME)[themeIndex]])) );
loadDiffTemp('templates/newEnd.html', text => newend = text );
loadDiffTemp('templates/newStringStart.html', text => startstring = text );
loadDiffTemp('templates/newStringEnd.html', text => endstring = text );

/*----themes----*/
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
    var metas = document.getElementsByTagName('meta')
    Object.keys(metas).forEach((key) => {
        if (metas[key].name === 'theme-color') {
            metas[key].content = THEME['--bc-primary'];
        }
    });

    /*-----Send theme to iframes-------*/
    let strTheme = getStrTheme(THEME);

    var welcomeIF = document.getElementById("page-welcome").contentWindow;
    welcomeIF.postMessage({ THEME, user: '<?php echo $userSelf; ?>' }, window.origin);

    if (diffstart !== undefined && newstart !== undefined) {
        diffstart = setStrTheme(diffstart, strTheme);
        newstart = setStrTheme(newstart, strTheme);
    }
    if(document.getElementById("page").srcdoc != "") {
        document.getElementById("page").srcdoc = setStrTheme(document.getElementById("page").srcdoc, strTheme);
    }
};
function changeTheme(select) {
    if (select === undefined) select = 0;
    setTheme(THEME[Object.keys(THEME)[select]]);
    if (document.getElementById('cpLink') !==  null) document.getElementById('cpLink').href = "https://swviewer.toolforge.org/php/control.php?themeIndex=" + select;
};

/*------Document variables------*/
const $descriptionContainer = document.getElementById('description-container');
const $queueDrawer = document.getElementById('queueDrawer');
const $floatingOverlay = document.getElementById('floatingOverlay');
const $sidebar = document.getElementById('sidebar');

/*------Sidebar-----*/
function openSidebar () {
    $sidebar.classList.add('sidebar-base__floating');
    $floatingOverlay.classList.add('floating-overlay__active');
}
function closeSidebar () {
    $sidebar.classList.remove('sidebar-base__floating');
    $floatingOverlay.classList.remove('floating-overlay__active');
}


/*------drawer-btn-------*/
var mDrawer;
function toggleMDrawer() { resizeDrawer(mDrawer, false); }
function resizeDrawer(state, start) {
    mDrawer = state;
    switch (mDrawer) {
        case 1:
        case 2: document.getElementById('eqBody').classList.add('eq__body__active');
            mDrawer = 0; break;
        default: document.getElementById('eqBody').classList.remove('eq__body__active');
            mDrawer = 1;
    }
    if (start !== true) $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: { 'action': 'set', query: 'mobile', mobile: state }, dataType: 'json'});
};
function closeMoreControl () {
    document.getElementById('moreControl').classList.add('more-control__hidden');
    document.getElementById('moreControlOverlay').classList.remove('more-control__overlay__active');
    document.getElementById('drawerFab').style.transform = 'scale(1)';
}
function toggleMoreControl () {
    var mc = document.getElementById('moreControl');
    var mcOverlay = document.getElementById('moreControlOverlay');
    if (mc.classList.contains('more-control__hidden')) {
        mc.classList.remove('more-control__hidden');
        mcOverlay.classList.add('more-control__overlay__active');
        document.getElementById('drawerFab').style.transform = 'scale(0)';
    } else { closeMoreControl(); }
}

/*------ Diff viewer -----*/

window.addEventListener('message', receiveMessage, false);
function receiveMessage(e) {
    if (e.origin !== 'https://swviewer.toolforge.org') return;

    if (e.data === undefined)
        e.source.postMessage($descriptionContainer.offsetHeight, window.origin);
    else if (e.data === true)
        $descriptionContainer.style.marginTop = (-1 * ($descriptionContainer.offsetHeight + 1)) + 'px';
    else if (e.data === false)
        $descriptionContainer.style.marginTop = '0px';
}
document.getElementById('page').onload = () => {
    $descriptionContainer.style.marginTop = '0px';
    try {
        Guesture.onSwipe(document.getElementById('page').contentDocument.body, "rightSwipe", () => openSidebar());
    } catch(e) {}
}

document.getElementById('loadingBar').style.width = "75%";



function addSandbox(sbList, wiki, page) {
    if (sbList.hasOwnProperty(wiki))
        sbList[wiki] = sbList[wiki] + ", " + page;
};

/*###################
------- Common -------
#####################*/

function scrollToBottom(id){
    if (document.getElementById(id) !== null) {
        document.getElementById(id).scrollTop = document.getElementById(id).scrollHeight;
    }
};

function classToggler (el, cssClass) {
    if (el.classList.contains(cssClass)) {
        return el.classList.remove(cssClass);
    }
    el.classList.add(cssClass);
}
function toggleTButton (button) { classToggler(button, 't-btn__active'); }
function toggleICheckBox (checkbox) { classToggler(checkbox, 'i-checkbox__active'); }
</script>
<script src="js/swv.js?v=4"></script>
<script>
/*#########################
--------- onLoad -------
#########################*/

window.onload = function() {
    document.getElementById('loadingBar').style.width = '100%';
    loadThemeList();
    if (themeIndex) {
        document.getElementById('themeSelector').selectedIndex = themeIndex;
        changeTheme(themeIndex);
    } else changeTheme(0);
    document.getElementById('loading').style.display = "none";
    document.getElementById('app').style.display = "block";
    
    $.getScript('https://swviewer.toolforge.org/js/modules/talk.js', () => removeTabNotice('btn-talk'));
    $.getScript('https://swviewer.toolforge.org/js/modules/logs.js', () => removeTabNotice('btn-logs'));
    $.getScript('https://swviewer.toolforge.org/js/modules/about.js', () => removeTabNotice('btn-about'));
    $.getScript('https://swviewer.toolforge.org/js/modules/notification.js', () => removeTabNotice('btn-notification'));
    
    Guesture.onSwipe(document.getElementById('page-welcome').contentDocument.body, "rightSwipe", () => openSidebar());
};
</script>
</body>
</html>