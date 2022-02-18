<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
$userName = $_SESSION['userName'];
session_write_close();

if (!isset($_POST["action"]) || !isset($_POST["query"])) {
    if (!isset($_GET["action"]) || !isset($_GET["query"]) || $_GET["action"] !== "get" || $_GET["query"] !== "all") {
        echo json_encode(["result" => "error", "info" => "Invalid request; dev. code 2"]);
        exit();
    }
}

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

if (!isset($_POST["action"])) {
    if ($_GET["action"] == "get" && $_GET["query"] = "all") {
        $q = $db->prepare('SELECT * FROM user WHERE name = :userName');
        $q->execute(array(':userName' => $userName));
        $result = $q->fetchAll();

        $response = ["userName" => $userName, "talkToken" => $result[0]['token'], "userRole" => $result[0]['userRole'], "isGlobalAccess" => $result[0]['isGlobalAccess'], "isGlobal" => $result[0]['isGlobal'], "local_wikis" => $result[0]['local_wikis'],   "checkmode" => $result[0]['checkmode'], "preset" => $result[0]['preset'], "lang" => $result[0]['lang'], "locale" => $result[0]['locale'], "hotkeys" => $result[0]['hotkeys'], "jumps" => $result[0]['jumps'], "sound" => $result[0]['sound'], "countqueue" => $result[0]['countqueue'], "terminateStream" => $result[0]['terminateStream'], "mobile" => $result[0]['mobile'], "direction" => $result[0]['direction'], "rhand" => $result[0]['rhand'], "defaultdelete" => $result[0]['defaultdelete'], "defaultwarn" => $result[0]['defaultwarn'], "theme" => $result[0]['theme']];
        echo json_encode($response);
        $db = null;
        exit();
    }
}

if ($_POST["action"] == "set") {
    if ($_POST["query"] == "theme") {
        if (isset($_POST['theme']) && isset($_POST["limit"])) {
            if ($_POST['theme'] >= "0" && $_POST['theme'] < $_POST["limit"]) {
                $q = $db->prepare('UPDATE user SET theme=:theme WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':theme' => $_POST['theme']));
            }
        }
    }

    if ($_POST["query"] == "swmt") {
        if (isset($_POST['swmt'])) {
            if ($_POST['swmt'] == "0" || $_POST['swmt'] == "1" || $_POST['swmt'] == "2") {
                $q = $db->prepare('UPDATE user SET swmt=:swmt WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':swmt' => $_POST['swmt']));
            }
        }
    }

    if ($_POST["query"] == "registered") {
        if (isset($_POST['registered'])) {
            if ($_POST['registered'] == "0" || $_POST['registered'] == "1") {
                $q = $db->prepare('UPDATE user SET registered=:registered WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':registered' => $_POST['registered']));
            }
        }
    }

    if ($_POST["query"] == "checkmode") {
        if (isset($_POST['checkmode'])) {
            if ($_POST['checkmode'] == "0" || $_POST['checkmode'] == "1" || $_POST['checkmode'] == "2") {
                $q = $db->prepare('UPDATE user SET checkmode=:checkmode WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':checkmode' => $_POST['checkmode']));
            }
        }
    }

    if (isset($_POST['lang'])) {
        if ($_POST['lang'] == null || preg_match('/^[a-zA-Z_\-]+$/', $_POST['lang'])) {
            $q = $db->prepare('UPDATE user SET lang=:lang WHERE name =:userName');
            $q->execute(array(':userName' => $userName, ':lang' => $_POST['lang']));
        }
    }

    if (isset($_POST['locale'])) {
        if ($_POST['locale'] == null || preg_match('/^[a-zA-Z_\-]+$/', $_POST['locale'])) {
            $q = $db->prepare('UPDATE user SET locale=:locale WHERE name =:userName');
            $q->execute(array(':userName' => $userName, ':locale' => $_POST['locale']));
        }
    }

    if ($_POST["query"] == "users") {
        if (isset($_POST['users'])) {
            if ($_POST['users'] == "0" || $_POST['users'] == "1" || $_POST['users'] == "2") {
                $q = $db->prepare('UPDATE user SET users=:users WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':users' => $_POST['users']));
            }
        }
    }

    if ($_POST["query"] == "anons") {
        if (isset($_POST['anons'])) {
            if ($_POST['anons'] == "0" || $_POST['anons'] == "1") {
                $q = $db->prepare('UPDATE user SET anons=:anons WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':anons' => $_POST['anons']));
            }
        }
    }

    if ($_POST["query"] == "rhand") {
        if (isset($_POST['rhand'])) {
            if ($_POST['rhand'] == "0" || $_POST['rhand'] == "1") {
                $q = $db->prepare('UPDATE user SET rhand=:rhand WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':rhand' => $_POST['rhand']));
            }
        }
    }

    if ($_POST["query"] == "terminateStream") {
        if (isset($_POST['terminateStream'])) {
            if ($_POST['terminateStream'] == "0" || $_POST['terminateStream'] == "1") {
                $q = $db->prepare('UPDATE user SET terminateStream=:terminateStream WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':terminateStream' => $_POST['terminateStream']));
            }
        }
    }

    if ($_POST["query"] == "mobile") {
        if (isset($_POST['mobile'])) {
            if ($_POST['mobile'] == "0" || $_POST['mobile'] == "1" || $_POST['mobile'] == "2" || $_POST['mobile'] == "3") {
                $q = $db->prepare('UPDATE user SET mobile=:mobile WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':mobile' => $_POST['mobile']));
            }
        }
    }

    if ($_POST["query"] == "preset") {
        if (isset($_POST['preset'])) {
            if ($_POST['preset'] == null || preg_match('/(\s|\w|\d)*?$/', $_POST['preset'])) {
                $q = $db->prepare('UPDATE user SET preset=:preset WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':preset' => $_POST['preset']));
                echo json_encode(["result" => "Success"]);
            }
        }
    }

    if ($_POST["query"] == "newbies") {
        if (isset($_POST['sqlnew'])) {
            if ($_POST['sqlnew'] == "0" || $_POST['sqlnew'] == "1") {
                $q = $db->prepare('UPDATE user SET new=:new WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':new' => $_POST['sqlnew']));
            }
        }
    }

    if (isset($_POST['defaultdelete'])) {
        if ($_POST['defaultdelete'] == null || preg_match('/^[a-zA-Z_\-,]+$/', $_POST['defaultdelete'])) {
            $q = $db->prepare('UPDATE user SET defaultdelete=:defaultdelete WHERE name =:userName');
            $q->execute(array(':userName' => $userName, ':defaultdelete' => $_POST['defaultdelete']));
        }
    }

    if (isset($_POST['defaultwarn'])) {
        if ($_POST['defaultwarn'] == null || preg_match('/^[a-zA-Z_\-,]+$/', $_POST['defaultwarn'])) {
            $q = $db->prepare('UPDATE user SET defaultwarn=:defaultwarn WHERE name =:userName');
            $q->execute(array(':userName' => $userName, ':defaultwarn' => $_POST['defaultwarn']));
        }
    }

    if ($_POST["query"] == "onlynew") {
        if (isset($_POST['onlynew'])) {
            if ($_POST['onlynew'] == "0" || $_POST['onlynew'] == "1") {
                $q = $db->prepare('UPDATE user SET onlynew=:onlynew WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':onlynew' => $_POST['onlynew']));
            }
        }
    }

    if ($_POST["query"] == "direction") {
        if (isset($_POST['direction'])) {
            if ($_POST['direction'] == "0" || $_POST['direction'] == "1") {
                $q = $db->prepare('UPDATE user SET direction=:direction WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':direction' => $_POST['direction']));
            }
        }
    }

    if ($_POST["query"] == "sound") {
        if (isset($_POST['sound'])) {
            if ($_POST['sound'] == "0" || $_POST['sound'] == "1" || $_POST['sound'] == "2" || $_POST['sound'] == "4" || $_POST['sound'] == "4" || $_POST['sound'] == "5") {
                $q = $db->prepare('UPDATE user SET sound=:sound WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':sound' => $_POST['sound']));
            }
        }
    }

    if ($_POST["query"] == "hotkeys") {
        if (isset($_POST['hotkeys'])) {
            if ($_POST['hotkeys'] == "0" || $_POST['hotkeys'] == "1") {
                $q = $db->prepare('UPDATE user SET hotkeys=:hotkeys WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':hotkeys' => $_POST['hotkeys']));
            }
        }
    }

    if ($_POST["query"] == "jumps") {
        if (isset($_POST['jumps'])) {
            if ($_POST['jumps'] == "0" || $_POST['jumps'] == "1") {
                $q = $db->prepare('UPDATE user SET jumps=:jumps WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':jumps' => $_POST['jumps']));
            }
        }
    }

    if ($_POST["query"] == "numbers") {
        if (isset($_POST['editscount'])) {
            if (preg_match('/^[0-9]+$/', $_POST['editscount'])) {
                $q = $db->prepare('UPDATE user SET editscount=:editscount WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':editscount' => $_POST['editscount']));
            }
        }
        if (isset($_POST['regdays'])) {
            if (preg_match('/^[0-9]+$/', $_POST['regdays'])) {
                $q = $db->prepare('UPDATE user SET regdays=:regdays WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':regdays' => $_POST['regdays']));
            }
        }
        if (isset($_POST['countqueue'])) {
            if (preg_match('/^[0-9]+$/', $_POST['countqueue'])) {
                $q = $db->prepare('UPDATE user SET countqueue=:countqueue WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':countqueue' => $_POST['countqueue']));
            }
        }
    }

    if ($_POST["query"] == "namespaces") {
        if (isset($_POST['ns'])) {
            if (preg_match('/^[0-9,]+$/', $_POST['ns']) || $_POST['ns'] == null || $_POST['ns'] == "") {
                $q = $db->prepare('UPDATE user SET namespaces=:ns WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':ns' => $_POST['ns']));
            }
        }
    }

    if ($_POST["query"] == "whitelist") {
        if (isset($_POST['wlusers'])) {
            $q = $db->prepare('UPDATE user SET wlusers=:wlusers WHERE name =:userName');
            $q->execute(array(':userName' => $userName, ':wlusers' => $_POST['wlusers']));
        }
        if (isset($_POST['wlprojects'])) {
            if ($_POST['wlprojects'] == null || preg_match('/^[a-zA-Z_\-,]+$/', $_POST['wlprojects'])) {
                $q = $db->prepare('UPDATE user SET wlprojects=:wlprojects WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':wlprojects' => $_POST['wlprojects']));
            }
        }
    }

    if ($_POST["query"] == "blacklist") {
        if (isset($_POST['blprojects'])) {
            if ($_POST['blprojects'] == null || preg_match('/^[a-zA-Z_\-,]+$/', $_POST['blprojects'])) {
                $q = $db->prepare('UPDATE user SET blprojects=:blprojects WHERE name =:userName');
                $q->execute(array(':userName' => $userName, ':blprojects' => $_POST['blprojects']));
            }
        }
    }

}
$db = null;
?>