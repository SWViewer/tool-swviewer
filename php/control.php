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
        <link href='https://tools-static.wmflabs.org/fontcdn/css?family=Roboto|Montserrat' rel='stylesheet' type='text/css'>
        <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
        <style>
            td {padding:5px}
            table {margin-bottom:10px; width: 100%; margin-top: 10px}
            body {padding:8px}
        </style>
    </head>
<body>
<h2 style="text-align:center">
    SWV control panel
</h2>
<div style="width:45%; display:inline-block; vertical-align: top; padding:10px;">
<h3 style="text-align:center">Status</h3>
<div style="display:inline-block">SSE stream:</div><div id="status-true" style="margin-left:5px; color:green; display:none">True</div><div id="status-false" style="margin-left:5px; color:red; display:none"> False</div>
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

<div style="width:45%; display:inline-block; vertical-align: top; padding:10px;">
<h3 style="text-align:center">The Talk</h3>
The Talk contains <?php if ($rows_talk == null || $rows_talk == "") echo "0"; else echo $rows_talk; ?> message(s) by <?php if ($days_talk == null || $days_talk == "") echo "0"; else echo $days_talk; ?> day(s).
<button style='color:green; height:18px' id='talk-btn'>Remove</button> messages older than 3 days.
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

<div style="width:45%; display:inline-block; vertical-align: top; padding:10px;">
<h3 style="text-align:center">Config verifi</h3>

<?php
if ($ver_rev < $last_rev) {
    echo "
<font color=red>We have unverified revisions in the Congfig.</font> <font color=black>Last user: </font><font color=green>".htmlspecialchars($last_user, ENT_QUOTES, 'UTF-8')."</font><br><br>
Please open <a href='https://meta.wikimedia.org/w/index.php?title=SWViewer/config.json&type=revision&diff=".$last_rev."&oldid=".$ver_rev."' target='_blank'>that link</a> and 
<button style='color:green; height:18px' id='verify-btn'>Confirm revisions</button>.<br>
If that revisions is bad, then please fix errors / revert, reload this page and confirm you own revision.

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
else
    echo "<font color=green>We do not have unverified revisions in the Congfig.</font>"
?>
</div>

<div style="width:45%; display:inline-block; vertical-align: top; padding:10px;">
<h3 style="text-align:center">List of users from global groups</h3>
<label id='globalFile'><span style='color: orange'>Loading...</span></label>
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

<div style="width:45%; display:inline-block; vertical-align: top; padding:10px;">
<h3 style="text-align:center">Blocked</h3>
These users are banned in SWV.<br>
<input type="text" id="addLocked"></input><button id="locked-btn">Add</button><button id="unlocked-btn">Remove</button><br>
<div style="width:100%; height: 250px;">
<table border="1">
<tr>
    <th>Username</th>
</tr>
<?php $q = $db->query('SELECT name FROM user WHERE locked=1');
while($row = $q -> fetch()) {
    echo "<tr><td>".htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'). "</td></tr>";
} ?>
</table>
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
</body>
<?php
$db = null;
exit(0);
?>