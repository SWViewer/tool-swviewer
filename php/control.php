<?php
    header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
    header('Content-Type: text/html; charset=utf-8');
    session_name( 'SWViewer' );
    session_start();
    if ((isset($_SESSION['tokenKey']) == false) or (isset($_SESSION['tokenSecret']) == false) or (isset($_SESSION['userName']) == false)) {
        echo "Log in please.";
        session_write_close();
        exit(0);
    }
    $userName = $_SESSION['userName'];
    session_write_close();
    if ($userName !== "Ajbura" && $userName !== "Iluvatar" && $userName !== "1997kB") {
        echo "Access denied";
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
            $q = $db->prepare('DELETE from talk WHERE msgtime < NOW() - INTERVAL 3 DAY');
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

    $db = null;
    exit(0);
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
        <link rel="manifest" href="../site.webmanifest">
        <link rel="mask-icon" href="../img/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name="apple-mobile-web-app-title" content="SWViewer">
        <meta name="application-name" content="SWViewer">
        <meta name="author" content="Iluvatar, ajbura, 1997kB">
        <meta name="description" content="SWV control panel">
        <meta name="keywords" content="SWMT, stats">
        <link href='//tools-static.wmflabs.org/fontcdn/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <link rel="stylesheet" href="./../css/variables.css">
        <link rel="stylesheet" href="./../css/base.css">
        <link rel="stylesheet" href="./../css/modules.css">
        <style>
            .base-container {
                max-width: 750px;
                margin: auto;
                padding: var(--side-padding);
                padding-top: 0;
            }
            .base-container > div { margin-top: 24px; }
        </style>
    </head>
    <body class="secondary-scroll">
        <div class="base-container secondary-cont">
            <div class="action-header__sticky" style="background-color: var(--bc-secondary);">
                <span class="action-header__title fs-xl" style="padding-left: 0;">SWV Control Panel</span>
            </div>
            <div>
                <label class="fs-lg">Status</label>
                <span class="fs-sm">SSE stream:</span>
                <span id="status-true" style="margin-left:2px; color:green; display:none">True</span>
                <span id="status-false" style="margin-left:2px; color:red; display:none">False</span>
            </div>
            <script>
                var source = new EventSource('https://stream.wikimedia.org/v2/stream/recentchange');
                source.addEventListener('open', function(e) {
                document.getElementById("status-false").style.display = "none";
                document.getElementById("status-true").style.display = "inline-block";
                }, false);

                source.addEventListener('error', function(e) {
                    document.getElementById("status-true").style.display = "none";
                    document.getElementById("status-false").style.display = "inline-block";
                }, false);
            </script>

            <div>
                <label class="fs-lg">The Talk</label>
                <span class="fs-sm">The Talk contains 
                    <?php if ($rows_talk == null || $rows_talk == "") echo "0"; else echo $rows_talk; ?> message(s) from last 
                    <?php if ($days_talk == null || $days_talk == "") echo "0"; else echo $days_talk; ?> day(s).
                </span>
                <span class="fs-sm"><span id="talk-btn" style="color: var(--link-color); cursor: pointer;">Click here</span> to remove message(s) older than 3 days.</span>
            </div>
            <script>
                document.getElementById('talk-btn').onclick = function() {
                    $.ajax({
                        url: 'control.php',
                        type: 'POST',
                        crossDomain: true,
                        data: {
                            action: 'talk',
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                };
            </script>

            <div>
                <label class="fs-lg">Verify config</label>

                <?php
                    if ($ver_rev < $last_rev) {
                        echo "
                    <span class='fs-sm' style='color: red;'>We have unverified revisions in the config.</span>
                    <span class='fs-sm'>Last user: </span><span class='fs-sm' style='color: green;'>".htmlspecialchars($last_user, ENT_QUOTES, 'UTF-8')."</span><br><br>
                    <span class='fs-sm'>Please open <a href='https://meta.wikimedia.org/w/index.php?title=SWViewer/config.json&type=revision&diff=".$last_rev."&oldid=".$ver_rev."' target='_blank'>this link</a> and confirm revisions.</span>
                    <span class='fs-sm'>If this revision is bad, then please fix errors/revert, reload this page and confirm your own revision.</span><br><br>
                    <button class='i-btn__accent accent-hover fs-md' id='verify-btn'>Confirm revisions</button>

                    <script>
                        document.getElementById('verify-btn').onclick = function() {
                            $.ajax({
                                url: 'control.php',
                                type: 'POST',
                                crossDomain: true,
                                data: {
                                    action: 'verify',
                                    id: ".$last_rev.",
                                    user: '".$userName."'
                                },
                                success: function() {
                                    location.reload();
                                }
                            });
                        }
                    </script>";
                    }
                    else echo "<span class='fs-sm' style='color: green;'>We do not have unverified revisions in the config.</span>"
                ?>
            </div>

            <div>
                <label class='fs-lg'>List of users from global groups</label>
                <span id='globalFile' class="fs-sm"><span style='color: orange'>Loading...</span></span>
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
                catch(e) {
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
                catch(e) {
                    globalFileCheck = false;
                    globalerr = "Download failed";
                }

                var timestamp = Number(new Date().getTime())/1000-46800;
                if (globaltime<=timestamp) {
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
                    <div class="i__title fs-lg">Block Users</div>
                    <div class="i__description fs-sm">Add users to block list.</div>
                    <div class="i__content fs-sm">
                        <div id="unlocked-btn" class="i-minus fs-sm">-</div>
                        <input id="addLocked" class="i-input__secondary secondary-placeholder fs-sm" type="text" name="addLocked" placeholder="User">
                        <div id="locked-btn" class="i-plus fs-sm">+</div>
                    </div>
                    <div class="i__extra">
                        <ul class="i-chip-list fs-sm">
                            <?php $q = $db->query('SELECT name FROM user WHERE locked=1');
                                while($row = $q -> fetch()) {
                                    echo "<li style='padding-left: 8px;'>".htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'). "</li>";
                                }
                            ?>
                        </ul>
                    </div>
                </div>

                <script>
                    document.getElementById('locked-btn').onclick = function() {

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
                                success: function() {
                                    location.reload();
                                }
                            });
                        }
                    }

                    document.getElementById('unlocked-btn').onclick = function() {

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
                                success: function() {
                                    location.reload();
                                }
                            });
                        }
                    }
                </script>
            </div>
        </div>
    </body>
</html>
<?php
    $db = null;
    exit(0);
?>