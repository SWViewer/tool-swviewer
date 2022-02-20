<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: text/html; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    echo "Log in please.";
    session_write_close();
    exit();
}
$userName = $_SESSION['userName'];
session_write_close();
if ($userName !== "Ajbura" && $userName !== "Iluvatar" && $userName !== "1997kB") {
    echo "Access denied";
    exit();
}

if (isset($_GET["restart"])) {
    $serverToken = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["serverTokenTalk"];
    $context = stream_context_create(array('http' => array('method' => 'GET', 'header' => "auth: " . $serverToken . "\r\n" . "User-Agent: swviewer.toolforge\r\n")));
    file_get_contents("https://swviewer-service.toolforge.org/restart", false, $context);
    exit();
}

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

if (isset($_POST["action"])) {
    if ($_POST["action"] == "verify")
        if (isset($_POST["id"]) && isset($_POST["user"])) {
            $q = $db->prepare('UPDATE verify SET id=:id, user=:user');
            $q->execute(array(':id' => $_POST['id'], ':user' => $_POST['user']));
        }

    if ($_POST["action"] == "talk") {
        $q = $db->prepare('DELETE FROM talk WHERE msgtime >= NOW() - INTERVAL 3 DAY');
        $q->execute();
    }


    if ($_POST["action"] == "lock")
        if (isset($_POST["user"])) {
            $q = $db->prepare('UPDATE user SET locked=1 WHERE name=:name');
            $q->execute(array(':name' => $_POST['user']));
        }


    if ($_POST["action"] == "unlock")
        if (isset($_POST["user"])) {
            $q = $db->prepare('UPDATE user SET locked=0 WHERE name=:name');
            $q->execute(array(':name' => $_POST['user']));
        }

    if ($_POST["action"] == "rebind")
        if (isset($_POST["user"])) {
            $q = $db->prepare('UPDATE user SET rebind=1 WHERE name=:name');
            $q->execute(array(':name' => $_POST['user']));
        }

    if ($_POST["action"] == "addBetaTester")
        if (isset($_POST["user"])) {
            $q = $db->prepare('UPDATE user SET betaTester=1 WHERE name=:name');
            $q->execute(array(':name' => $_POST['user']));
        }


    if ($_POST["action"] == "removeBetaTester")
        if (isset($_POST["user"])) {
            $q = $db->prepare('UPDATE user SET betaTester=0 WHERE name=:name');
            $q->execute(array(':name' => $_POST['user']));
        }


    $db = null;
    exit();
}

$q = $db->prepare('SELECT * FROM verify');
$q->execute();
$result = $q->fetchAll();

$ver_rev = $result[0]["id"];
$ver_user = $result[0]["user"];
$ver_date = $result[0]["date"];

$q = $db->prepare('SELECT DATEDIFF(MAX(msgtime), MIN(msgtime)) AS days FROM talk');
$q->execute();
$result = $q->fetchAll();
$days_talk = $result[0]["days"];

$q = $db->prepare('SELECT COUNT(*) AS rows FROM talk');
$q->execute();
$result = $q->fetchAll();
$rows_talk = $result[0]["rows"];

$url = "https://meta.wikimedia.org/w/api.php?action=query&prop=revisions&titles=SWViewer/config.json&rvslots=*&format=json&utf8=1";
$content = @file_get_contents($url);

if ($content === FALSE) {
    echo "Error! Loading ID of revision is not success";
    exit(0);
}

$content = json_decode($content, true);
$last_rev = $content["query"]["pages"][10795717]["revisions"][0]["revid"];
$last_user = $content["query"]["pages"][10795717]["revisions"][0]["user"];
$last_date = $content["query"]["pages"][10795717]["revisions"][0]["timestamp"];
?>

    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>SWV CP</title>

        <link rel="apple-touch-icon" sizes="180x180" href="../img/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../img/favicons/favicon-16x16.png">
        <link rel="mask-icon" href="../img/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name="apple-mobile-web-app-title" content="SWViewer">
        <meta name="application-name" content="SWViewer">
        <meta name="author" content="Iluvatar, ajbura, 1997kB">
        <meta name="description" content="SWV control panel">
        <meta name="keywords" content="SWMT, stats">
        <meta name="theme-color" content="#191919">
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <link rel="stylesheet" href="../css/base/fonts.css">
        <link rel="stylesheet" href="../css/base/variables.css">
        <link rel="stylesheet" href="../css/base/base.css">
        <link rel="stylesheet" href="../css/components/comp.css">
        <link rel="stylesheet" href="../css/components/header.css">
        <style>
            .base-container > div {
                margin-top: 24px;
            }
        </style>
    </head>
    <body class="secondary-scroll">
        <div class="action-header__sticky primary-cont" style="background-color: var(--bc-primary); box-shadow: var(--floatbar-shadow)">
            <span class="left-align-container action-header__title fs-xl">SWV Control Panel</span>
        </div>
        <div class="left-align-container base-container secondary-cont">
            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">Status</div>
                    <div class="i__description fs-sm">
                        <span>SSE stream:</span>
                        <span id="status-true" style="margin-left:2px; color:green; display:none">True</span>
                        <span id="status-false" style="margin-left:2px; color:red; display:none">False</span>
                    </div>
                </div>
            </div>
            <script>
                var source = new EventSource('https://stream.wikimedia.org/v2/stream/recentchange');
                source.addEventListener('open', function (e) {
                    document.getElementById("status-false").style.display = "none";
                    document.getElementById("status-true").style.display = "inline-block";
                }, false);

                source.addEventListener('error', function (e) {
                    document.getElementById("status-true").style.display = "none";
                    document.getElementById("status-false").style.display = "inline-block";
                }, false);
            </script>

            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">The Talk</div>
                    <div class="i__description fs-sm">
                        <span>The Talk contains
                            <?php if ($rows_talk == null || $rows_talk == "") echo "0"; else echo $rows_talk; ?> message(s) from last
                            <?php if ($days_talk == null || $days_talk == "") echo "0"; else echo $days_talk; ?> day(s).
                            You can remove message(s) of only last 3 days.
                        </span>
                    </div>
                    <div class="i__extra" style="padding-top: 8px;">
                        <button id="talk-btn" class='i-btn__accent accent-hover fs-md'>Remove messages</button>
                    </div>
                </div>
            </div>
            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">Restart</div>
                    <div class="i__description fs-sm">
                        <span>
                            You can restart server.
                        </span>
                    </div>
                    <div class="i__extra" style="padding-top: 8px;">
                        <button id="restart-btn" class='i-btn__accent accent-hover fs-md'>Restart server</button>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('talk-btn').onclick = function () {
                    if (confirm('Delete latest messages. Are you sure?')) $.ajax({
                        url: 'control.php', type: 'POST',
                        crossDomain: true,
                        data: {
                            action: 'talk',
                        },
                        success: function () {
                            location.reload();
                        }
                    });
                };

                document.getElementById('restart-btn').onclick = function () {
                    $.ajax({url: 'control.php?restart=1', type: 'GET', dataType: 'json'});
                   document.getElementById('restart-btn').textContent = "restarting...";
                };
            </script>

            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">Verify config</div>
                    <?php
                        if ($ver_rev < $last_rev) {
                            echo "<div class='i__description fs-sm'>
                                    <span style='color: red;'>We have unverified revisions in the config.</span>
                                    <span>Last user: </span><span style='color: green;'>" . htmlspecialchars($last_user, ENT_QUOTES, 'UTF-8') . "</span><br>
                                    <span>If this revision is bad, then please fix errors/revert, reload this page and confirm your own revision.</span>
                                </div>
                                
                                <div class='i__extra' style='padding-top: 8px; display: flex; flex-wrap: wrap;'>
                                    <button id='verify-btn' class='i-btn__accent accent-hover fs-md' style='margin: 0 16px 16px 0;'>Confirm revisions</button>
                                    <button onclick='window.open(\"https://meta.wikimedia.org/w/index.php?title=SWViewer/config.json&type=revision&diff=" . $last_rev . "&oldid=" . $ver_rev . "\")' class='i-btn__secondary-outlined secondary-hover fs-md'>Review revisions</button>
                                </div>
                                

                                <script>
                                    document.getElementById('verify-btn').onclick = function() {
                                        if (confirm('Confirm revisions. Are you sure?')) $.ajax({
                                            url: 'control.php',
                                            type: 'POST',
                                            crossDomain: true,
                                            data: {
                                                action: 'verify',
                                                id: " . $last_rev . ",
                                                user: '" . $userName . "'
                                            },
                                            success: function() {
                                                location.reload();
                                            }
                                        });
                                    }
                                </script>";
                        } else echo "<div class='i__description fs-sm' style='color: green;'>We do not have unverified revisions in the config.</div>"
                    ?>
                </div>
            </div>

            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">List of users from global groups</div>
                    <div id='globalFile'  class="i__description fs-sm"><span style='color: orange'>Loading...</span></div>
                </div>
            </div>

            <script>
                var global = [];
                var globalFileCheck = true;
                var globalerr = "";
                var xhr = new XMLHttpRequest();
                try {
                    xhr.open('POST', "../lists/globalUsers.txt", false);
                    xhr.send();
                    global = xhr.responseText.slice(0, -1).split(",");
                }
                catch (e) {
                    globalFileCheck = false;
                    globalerr = "Download failed";
                }
                if (global.length < 5) {
                    globalFileCheck = false;
                    if (globalerr !== "Download failed")
                        globalerr = "Too few items";
                }
                var globaltime = Number(0);
                try {
                    xhr.open('POST', "../lists/globalUsersLastUpdate.txt", false);
                    xhr.send();
                    globaltime = Number(xhr.responseText);
                }
                catch (e) {
                    globalFileCheck = false;
                    globalerr = "Download failed";
                }

                var timestamp = Number(new Date().getTime()) / 1000 - 46800;
                if (globaltime <= timestamp) {
                    globalFileCheck = false;
                    if (globalerr !== "Download failed")
                        globalerr = "Outdated";
                }


                if (globalFileCheck === false)
                    document.getElementById("globalFile").innerHTML = "<span style='color: red'>" + globalerr + "</span>";
                else
                    document.getElementById("globalFile").innerHTML = "<span style='color: green'>Normal</span>";
            </script>


            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">Rebind parameters for user</div>
                    <div class="i__description fs-sm">Forced parameters ipdate after next open.</div>
                    <div class="i__content fs-sm">
                        <input id="addrebind" class="i-input__secondary secondary-placeholder fs-sm" type="text"
                            name="addrebind" placeholder="User">
                        <div id="rebind-btn" class="i-plus fs-sm">+</div>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-lg">Block Users</div>
                    <div class="i__description fs-sm">Add users to block list.</div>
                    <div class="i__content fs-sm">
                        <div id="unlocked-btn" class="i-minus fs-sm">-</div>
                        <input id="addLocked" class="i-input__secondary secondary-placeholder fs-sm" type="text"
                            name="addLocked" placeholder="User">
                        <div id="locked-btn" class="i-plus fs-sm">+</div>
                    </div>
                    <div class="i__extra">
                        <ul class="i-chip-list fs-sm">
                            <?php $q = $db->query('SELECT name FROM user WHERE locked=1');
                            while ($row = $q->fetch()) {
                                echo "<li style='padding-left: 8px;'>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <script>
                    document.getElementById('locked-btn').onclick = function () {

                        var lockedUser = document.getElementById('addLocked').value;
                        if (lockedUser !== null && lockedUser !== "") {
                            $.ajax({
                                url: 'control.php',
                                type: 'POST',
                                crossDomain: true,
                                data: {
                                    action: 'lock',
                                    user: lockedUser
                                },
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    }

                    document.getElementById('unlocked-btn').onclick = function () {

                        var lockedUser = document.getElementById('addLocked').value;
                        if (lockedUser !== null && lockedUser !== "") {
                            $.ajax({
                                url: 'control.php',
                                type: 'POST',
                                crossDomain: true,
                                data: {
                                    action: 'unlock',
                                    user: lockedUser
                                },
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    }

                    document.getElementById('rebind-btn').onclick = function () {

                        var rebindUser = document.getElementById('addrebind').value;
                        if (rebindUser !== null && rebindUser !== "") {
                            $.ajax({
                                url: 'control.php',
                                type: 'POST',
                                crossDomain: true,
                                data: {
                                    action: 'rebind',
                                    user: rebindUser
                                },
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    }


                </script>
            </div>

            <div>
                <div class="i__base">
                    <div class="i__title fs-lg">Beta Tester</div>
                    <div class="i__description fs-sm">Add users to <a href="https://meta.wikimedia.org/wiki/SWViewer/members" target="_blank">beta tester list</a>.</div>
                    <div class="i__content fs-sm">
                        <div id="removeBetaTester-btn" class="i-minus fs-sm">-</div>
                        <input id="addTester" class="i-input__secondary secondary-placeholder fs-sm" type="text"
                            name="addTester" placeholder="User">
                        <div id="addBetaTester-btn" class="i-plus fs-sm">+</div>
                    </div>
                    <div class="i__extra">
                        <ul class="i-chip-list fs-sm">
                            <?php $q = $db->query('SELECT name FROM user WHERE betaTester=1');
                            while ($row = $q->fetch()) {
                                echo "<li style='padding-left: 8px;'>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <script>
                    document.getElementById('addBetaTester-btn').onclick = function () {

                        var betaUser = document.getElementById('addTester').value;
                        if (betaUser !== null && betaUser !== "") {
                            $.ajax({
                                url: 'control.php',
                                type: 'POST',
                                crossDomain: true,
                                data: {
                                    action: 'addBetaTester',
                                    user: betaUser
                                },
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    }

                    document.getElementById('removeBetaTester-btn').onclick = function () {

                        var betaUser = document.getElementById('addTester').value;
                        if (betaUser !== null && betaUser !== "") {
                            $.ajax({
                                url: 'control.php',
                                type: 'POST',
                                crossDomain: true,
                                data: {
                                    action: 'removeBetaTester',
                                    user: betaUser
                                },
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    }
                </script>
            </div>
        </div>
        <script>
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
                "Light": { '--bc-primary': '#ffffff', '--bc-primary-low': '#f6f6f6', '--bc-primary-hover': 'rgba(0, 0, 0, .1)',
                    ...BC_LIGHT, ...ICP_ON_LIGHT, ...ICS_ON_LIGHT, ...BCA_LIGHT, ...TCP_ON_LIGHT, ...TCS_ON_LIGHT,...THEME_FIX },
                "Dark": { '--bc-primary': '#0f1115', '--bc-primary-low': '#15171d', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
                    '--bc-secondary': '#1c1e26', '--bc-secondary-low': '#21242c', '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
                    ...ICP_ON_DARK, ...ICS_ON_DARK, ...BCA_DARK, ...TCP_ON_DARK, ...TCS_ON_DARK, ...THEME_FIX },
                "AMOLED": { '--bc-primary': '#000000', '--bc-primary-low': '#050505', '--bc-primary-hover': 'rgba(255, 255, 255, .05)',
                    '--bc-secondary': '#000000', '--bc-secondary-low': '#111111', '--bc-secondary-hover': 'rgba(255, 255, 255, .05)',
                    ...ICP_ON_DARK, ...ICS_ON_DARK, ...BCA_DARK, ...TCP_ON_DARK, ...TCS_ON_DARK, ...THEME_FIX },
                "System default": { },
            }
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
            }
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            const themeIndex = parseInt(urlParams.get('themeIndex'));
            
            function setSystemDefaultTheme() {
                let systemTheme = window.getComputedStyle(document.documentElement).getPropertyValue('--system-theme');
                if (systemTheme == 'dark') setTheme(THEME[Object.keys(THEME)[2]]);
                else setTheme(THEME[Object.keys(THEME)[0]]);
            }
            
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                if (themeIndex !== 4) return;
                setSystemDefaultTheme();
            });

            if(themeIndex !== null && !isNaN(themeIndex)) {
                if (themeIndex === 4) setSystemDefaultTheme();
                else setTheme(THEME[Object.keys(THEME)[themeIndex]]);
            }
        </script>
    </body>
    </html>
<?php
$db = null;
?>
