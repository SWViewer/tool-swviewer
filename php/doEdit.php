<?php
require_once 'includes/headerOAuth.php';
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_POST["page"]) || !isset($_POST["wiki"]) || !isset($_POST["project"]) || !isset($_POST["text"]) || !isset($_POST["summary"]) || (!isset($_POST["warn"]) && !isset($_POST["basetimestamp"]))) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
$userName = $_SESSION['userName'];
session_write_close();

$page = $_POST["page"];
$apiUrl = $_POST["project"];
$text = $_POST["text"];
$summary = $_POST["summary"];
$wiki = $_POST["wiki"];

if (isset($_POST["checkreport"])) {
    if (isset($_POST["regexreport"]) && isset($_POST["user"])) {
        $apiUrl = str_replace("/api.php", "/index.php", $apiUrl);
        $res_content = @file_get_contents($apiUrl . "?action=raw&title=" . urlencode($page));
        $apiUrl = str_replace("/index.php", "/api.php", $apiUrl);

        $regex = str_replace("$1", preg_quote($_POST["user"]), $_POST["regexreport"]);
        $regex2 = $regex;
        if (isset($_POST["regexreport2"]))
            if ($_POST["regexreport2"] !== "" && $_POST["regexreport2"] !== null)
                $regex2 = str_replace("$1", preg_quote($_POST["user"]), $_POST["regexreport2"]);
        echo (preg_match("/" . $regex . "/", $res_content) || preg_match("/" . $regex2 . "/", $res_content)) ? json_encode(["result" => true]) : json_encode(["result" => false]);
    }
    exit();
}
$params = ['action' => 'query', 'meta' => 'tokens', 'type' => 'csrf', 'format' => 'json'];
$token = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params))->query->tokens->csrftoken;

if (isset($_POST["getfirstuser"])) {
    $params = ['format' => 'json', 'utf8' => '1', 'action' => 'query', 'titles' => $page, 'prop' => 'revisions', 'rvprop' => 'user', 'rvslots' => '*', 'rvlimit' => 1, 'rvdir' => 'newer'];
    $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
    $res2 = null;
    forEach ($res->query->pages as $key => $p) {
        if ($key !== "-1")
            $res2 = $p;
    }
    if ($res2 !== null)
        if (isset($res2->revisions[0]->user))
            echo json_encode(["result" => "sucess", "user" => $res2->revisions[0]->user]);
    exit();
}

if (isset($_POST["warn"])) {
    $sectiontitle = $_POST["sectiontitle"];
    if ($_POST["warn"] == "rollback") {
        if ($_POST["withoutsection"] == "true") {
            $params = ['format' => 'json', 'utf8' => '1', 'action' => 'query', 'prop' => 'revisions', 'rvprop' => 'size', 'titles' => $page];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            $res2 = null;
            forEach ($res->query->pages as $key => $p) {
                if ($key !== "-1")
                    $res2 = $p;
            }
            if ($res2 !== null)
                if ($res2->revisions[0]->size !== "0")
                    $text = "\n\n" . $text;
            $params = ['action' => 'edit', 'title' => $page, 'appendtext' => $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        } else {
            $params = ['action' => 'parse', 'page' => $page, 'prop' => 'sections', 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            $sectionNumber = "new";
            if (isset($res->parse))
                if (isset($res->parse->sections))
                    forEach ($res->parse->sections as $section) {
                        if (isset($section->line))
                            if (isset($section->index))
                                if ($section->line == $sectiontitle)
                                    $sectionNumber = $section->index;
                    }
            if ($sectionNumber !== "new") {
                $apiUrl = str_replace("/api.php", "/index.php", $apiUrl);
                $res_content = @file_get_contents($apiUrl . "?action=raw&title=" . urlencode($page) . "&section=" . $sectionNumber);
                $apiUrl = str_replace("/index.php", "/api.php", $apiUrl);
                $text = $res_content . "\n\n" . $text;
                $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => $sectionNumber, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
                $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            } else {
                $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
                $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            }
        }
        if (isset($res->edit->title)) {
            if (isset($res->edit->nochange)) {
                echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
                exit();
            }
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'warn', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $q = $db->prepare('UPDATE stats SET warn=warn + 1 WHERE user=:username');
            $q->execute(array(':username' => $userName));
            $db = null;

            echo json_encode(["result" => "sucess"]);
        }
    }

    if ($_POST["warn"] == "speedy") {
        if (isset($sectiontitle) && $sectiontitle !== "" && $sectiontitle !== null) {
            $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        } else {
            $params = ['action' => 'query', 'prop' => 'revisions', 'rvprop' => 'size', 'titles' => $page, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            $res2 = null;
            forEach ($res->query->pages as $key => $p) {
                if ($key !== "-1")
                    $res2 = $p;
            }
            if ($res2 !== null)
                if ($res2->revisions[0]->size !== "0")
                    $text = "\n\n" . $text;
            $params = ['action' => 'edit', 'title' => $page, 'appendtext' => $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        }
        if (isset($res->edit->title)) {
            if (isset($res->edit->nochange)) {
                echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
                exit();
            }

            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'warn', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;
            echo json_encode(["result" => "sucess"]);
        }
    }

    if ($_POST["warn"] == "report") {
        if ($_POST["withoutsection"] !== "true") {
            if (isset($_POST["top"]) && $_POST["top"] == "true") {
                $preamb = (isset($_POST["preamb"]) && $_POST["preamb"] == "true") ? "\n\n" : "";
                $params = ['action' => 'edit', 'title' => $page, 'appendtext' => $preamb . "== " . $sectiontitle . " ==" . "\n" . $text, 'section' => 0, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
                $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            } else {
                $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
                $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            }
        } else {
            if (isset($_POST["top"]) && $_POST["top"] == "true") {
                $preamb = (isset($_POST["preamb"]) && $_POST["preamb"] == "true") ? "\n\n" : "";
                $params = ['action' => 'edit', 'title' => $page, 'appendtext' => $preamb . $text, 'section' => 0, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
                $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            } else {
                $params = ['action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
                $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
            }
        }
        if (isset($res->edit->title)) {
            if (isset($res->edit->nochange)) {
                echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
                exit();
            }

            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'report', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $q = $db->prepare('UPDATE stats SET report=report + 1 WHERE user=:username');
            $q->execute(array(':username' => $userName));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }

    if ($_POST["warn"] == "protect") {
        if ($_POST["withoutsection"] !== "true") {
            $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        } else {
            $params = ['action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => 1, 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        }

        if (isset($res->edit->title)) {
            if (isset($res->edit->nochange)) {
                echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
                exit();
            }
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'protect', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $q = $db->prepare('UPDATE stats SET protect=protect + 1 WHERE user=:username');
            $q->execute(array(':username' => $userName));
            $db = null;

            echo json_encode(["result" => "sucess"]);
        }
    }

    if ($_POST["warn"] == "SRG") {
        if ($_POST["withoutsection"] !== "true") {
            $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        } else {
            $params = ['action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        }

        if (isset($res->edit->title)) {
            if (isset($res->edit->nochange)) {
                echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
                exit();
            }
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'SRG', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            echo json_encode(["result" => "sucess"]);
        }
    }

    if ($_POST["warn"] == "SRM") {
        if ($_POST["withoutsection"] !== "true") {
            $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        } else {
            $params = ['action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token, 'utf8' => '1', 'format' => 'json'];
            $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
        }

        if (isset($res->edit->title)) {
            if (isset($res->edit->nochange)) {
                echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
                exit();
            }
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'SRM', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            echo json_encode(["result" => "sucess"]);
        }
    }

} else {
    $params = ['action' => 'edit', 'title' => $page, 'text' => $text, 'summary' => $summary, 'basetimestamp' => $_POST["basetimestamp"], 'nocreate' => '1', 'token' => $token, 'utf8' => '1', 'format' => 'json'];
    $res = json_decode($client->makeOAuthCall($accessToken, $apiUrl, true, $params));
}

if (!isset($res->edit->newrevid)) {
    if (isset($res->edit->nochange)) {
        echo json_encode(["code" => "alreadydone", "result" => "This edit has already made by someone."]);
        exit();
    }

    $res = json_decode(json_encode($res), True);
    if (isset($res["edit"]["info"]))
        echo json_encode(["result" => $res["edit"]["info"], "code" => $res["edit"]["code"]]);
    else
        echo (isset($res["error"]["info"])) ? json_encode(["result" => $res["error"]["info"], "code" => $res["error"]["code"]]) : json_encode(["result" => "Unknow error", "code" => "null"]);
    exit();
}

if (isset($res->edit->title) && !isset($_POST["warn"])) {
    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
    $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
    unset($ts_mycnf, $ts_pw);

    $actiontype = "edit"; $actiontype2 = "edits";
    if (isset($_POST["isdelete"]))
        if ($_POST["isdelete"] == "true") {
            $actiontype = "delete";
            $actiontype2 = "del"; 
        }
    $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
    $q->execute(array(':user' => $userName, ':type' => $actiontype, ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $apiUrl) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
    $q = $db->prepare('UPDATE stats SET '.$actiontype2.'='.$actiontype2.'+ 1 WHERE user=:username');
    $q->execute(array(':username' => $userName));
    $db = null;

    $res = json_decode(json_encode($res), True);
    echo json_encode(["result" => "Success", "summary" => $summary, "oldrevid" => $res["edit"]["oldrevid"], "newrevid" => $res["edit"]["newrevid"], "user" => $userName]);
}

?>