<?php
header('Content-Type: text/plain; charset=utf-8');
require_once '/data/project/swviewer/vendor/autoload.php';

use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;

$configFile = '/data/project/swviewer/security/config.php';

$config = require_once $configFile;
$conf = new ClientConfig($config['url']);
$conf->setConsumer(new Consumer($config['consumer_key'], $config['consumer_secret']));
$client = new Client($conf);
session_name('SWViewer');
session_start();

if (isset($_GET["action"])) {
    if ($_GET["action"] == "auth") {
        list($authUrl, $token) = $client->initiate();
        $_SESSION['request_key'] = $token->key;
        $_SESSION['request_secret'] = $token->secret;

        header("Location: $authUrl");
        exit();
    }
    if ($_GET["action"] == "unlogin") {
        $_SESSION = Array();
        session_write_close();
        setcookie("SWViewer-auth", null, time() - 1, "/", "swviewer.toolforge.org", TRUE, TRUE);
        if (isset($_GET["kik"])) {
            header("Location: https://swviewer.toolforge.org/");
            exit();
        } else
            echo "Unlogin is done";
        exit();
    }
}

if (!isset($_SESSION['request_key']) || !isset($_SESSION['request_secret']) || !isset($_GET['oauth_verifier'])) {
    $_SESSION = Array();
    session_write_close();
    setcookie("SWViewer-auth", null, time() - 1, "/", "swviewer.toolforge.org", TRUE, TRUE);
    header("Location: https://swviewer.toolforge.org/php/oauth.php?action=auth");
    exit();
}
$requestToken = new Token($_SESSION['request_key'], $_SESSION['request_secret']);
$accessToken = $client->complete($requestToken, $_GET['oauth_verifier']);

$_SESSION['tokenKey'] = $accessToken->key;
$_SESSION['tokenSecret'] = $accessToken->secret;
unset($_SESSION['request_key'], $_SESSION['request_secret']);

$apiUrl = preg_replace('/index\.php.*/', 'api.php', $config['url']);
$accessToken = new Token($accessToken->key, $accessToken->secret);

$ident = $client->identify($accessToken);
$_SESSION['userName'] = $ident->username;

$globalInfo = json_decode($client->makeOAuthCall($accessToken, "$apiUrl?action=query&meta=globaluserinfo&guiprop=groups|merged|editcount&guiuser=" . urlencode($ident->username) . "&utf8=1&format=json"), True);
$global = false;

$userRole = "none";
forEach ($globalInfo['query']['globaluserinfo']['groups'] as $globalGroup) {
    if ($globalGroup == 'steward' || $globalGroup == 'global-sysop' || $globalGroup == 'global-rollbacker') {
        $global = true;
        if ($globalGroup == 'steward')
            $userRole = "S";
        if ($globalGroup == 'global-sysop')
            $userRole = "GS";
    }
}
$_SESSION['projects'] = "";
if ($global == true || $ident->username == "Ajbura" || $ident->username == "Exoped")
    $_SESSION['mode'] = 'global';
else {
    $checkLocal = false;

    $patrollerGroup = ["dawiki", "enwikivoyage", "frwikisource", "frwiktionary", "hewiktionary", "hewiki", "hewikinews", "hewikibooks", "hrwiki", "itwikiversity", "itwikibooks", "itwikivoyage", "itwiktionary", "mkwiki", "nnwiki", "nowiki", "nowikibooks", "zhwikiversity", "zhwikivoyage", "metawiki", "bgwiki", "dawiki", "trwiki"];
    $editorGroup = ["plwiki", "zh_classicalwiki", "kawiki", "dewiki", "trwikiquote", "enwikibooks", "elwikinews", "enwikinews", "fawikinews", "huwiki", "plwikisource", "ptwikibooks"];
    $eliminatorGroup = ["fawiki", "viwiki", "viwikibooks"];
    $botAdminGroup = ["mlwiki", "ckbwiki", "frwiktionary"];
    $testSysopGroup = ["incubatorwiki"];
    $wikidataStaffGroup = ["wikidatawiki", "testwikidatawiki"];
    $curatorGroup = ["enwikiversity"];
    $wrongsysop = ['aawiki', 'aawiktionary', 'aawikibooks', 'abwiktionary', 'akwiktionary', 'akwikibooks', 'amwikiquote', 'angwikibooks', 'angwikiquote', 'angwikisource', 'aswiktionary', 'aswikibooks', 'astwikibooks', 'astwikiquote', 'avwiktionary', 'aywikibooks', 'bhwiktionary', 'biwiktionary', 'biwikibooks', 'bmwiktionary', 'bmwikibooks', 'bmwikiquote', 'bowiktionary', 'bowikibooks', 'chwiktionary', 'chwikibooks', 'chowiki', 'cowikibooks', 'cowikiquote', 'crwiktionary', 'crwikiquote', 'dzwiktionary', 'gawikibooks', 'gawikiquote', 'gnwikibooks', 'gotwikibooks', 'guwikibooks', 'howiki', 'htwikisource', 'huwikinews', 'hzwiki', 'iewikibooks', 'iiwiki', 'ikwiktionary', 'kjwiki', 'kkwikiquote', 'knwikibooks', 'krwiki', 'krwikiquote', 'kswikibooks', 'kswikiquote', 'kwwikiquote', 'lbwikibooks', 'lbwikiquote', 'lnwikibooks', 'lvwikibooks', 'mhwiki', 'mhwiktionary', 'miwikibooks', 'mnwikibooks', 'muswiki', 'mywikibooks', 'nawikibooks', 'nawikiquote', 'nahwikibooks', 'ndswikibooks', 'ndswikiquote', 'ngwiki', 'piwiktionary', 'pswikibooks', 'quwikibooks', 'quwikiquote', 'rmwiktionary', 'rmwikibooks', 'rnwiktionary', 'scwiktionary', 'sdwikinews', 'sewikibooks', 'simplewikibooks', 'simplewikiquote', 'snwiktionary', 'suwikibooks', 'swwikibooks', 'thwikinews', 'tkwikibooks', 'tkwikiquote', 'towiktionary', 'ttwikiquote', 'twwiktionary', 'ugwikibooks', 'ugwikiquote', 'uzwikibooks', 'vowikibooks', 'vowikiquote', 'wawikibooks', 'xhwiktionary', 'xhwikibooks', 'yowiktionary', 'yowikibooks', 'zawiktionary', 'zawikibooks', 'zawikiquote', 'zh_min_nanwikibooks', 'zh_min_nanwikiquote', 'zuwikibooks', 'advisorywiki', 'nzwikimedia', 'pa_uswikimedia', 'qualitywiki', 'strategywiki', 'tenwiki', 'usabilitywiki', 'vewikimedia', 'wikimania2005wiki', 'wikimania2006wiki', 'wikimania2007wiki', 'wikimania2008wiki', 'wikimania2009wiki', 'wikimania2010wiki', 'wikimania2011wiki', 'wikimania2012wiki', 'wikimania2013wiki', 'wikimania2014wiki', 'wikimania2015wiki', 'wikimania2016wiki', 'wikimania2017wiki', 'wikimania2018wiki'];
    $testWikis = ['testwiki', 'test2wiki', 'testwikidatawiki', 'testcommonswiki', 'labstestwiki'];
    $totalEdits = 0;
    $totalBlocks = 0;

    forEach ($globalInfo['query']['globaluserinfo']['merged'] as $localGroups) {
        if (array_key_exists('groups', $localGroups))
            forEach ($localGroups['groups'] as $localGroup) {
                if (($localGroup == 'rollbacker' || ($localGroup == 'sysop' && !in_array($localGroups['wiki'], $wrongsysop)) || ($localGroup == 'editor' && in_array($localGroups['wiki'], $editorGroup)) || ($localGroup == 'patroller' && in_array($localGroups['wiki'], $patrollerGroup)) || ($localGroup == 'eliminator' && in_array($localGroups['wiki'], $eliminatorGroup)) || ($localGroup == 'botadmin' && in_array($localGroups['wiki'], $botAdminGroup)) || ($localGroup == 'test-sysop' && in_array($localGroups['wiki'], $testSysopGroup)) || ($localGroup == 'wikidata-staff' && in_array($localGroups['wiki'], $wikidataStaffGroup)) || ($localGroup == 'curator' && in_array($localGroups['wiki'], $curatorGroup))) && (!in_array($localGroups['wiki'], $testWikis))) {
                    if (isset($_SESSION['projects']) && $_SESSION['projects'] !== "")
                        $_SESSION['projects'] .= $localGroups['wiki'] . ',';
                    else
                        $_SESSION['projects'] = $localGroups['wiki'] . ',';
                    $checkLocal = true;
                }
            }

        if (array_key_exists('editcount', $localGroups) && !in_array($localGroups['wiki'], $testWikis))
            $totalEdits += intval($localGroups['editcount']);
        if (array_key_exists('blocked', $localGroups))
            if (array_key_exists('expiry', $localGroups["blocked"]))
                if ($localGroups['blocked'] === "infinity" && !in_array($localGroups['wiki'], $testWikis))
                    $totalBlocks += 1;
    }
    if ($checkLocal == true) {
        $_SESSION['mode'] = 'local';

        if ($totalEdits >= 1000 && $totalBlocks < 2)
            $_SESSION['accessGlobal'] = 'true';
    } else {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://swviewer.toolforge.org/?error=rights");
        exit();
    }
}

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('SELECT name, token, lang FROM user WHERE name=:name');
$q->execute(array(':name' => $ident->username));
$resToken = $q->fetchAll();
$accessGlobal = null;
$accessGlobalSQL = 0;
if (isset($_SESSION['accessGlobal'])) {
    $accessGlobal = $_SESSION['accessGlobal'];
    $accessGlobalSQL = 1;
}
$isGlobal = 0;
if ($_SESSION['mode'] == 'global')
    $isGlobal = 1;
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); if ($lang === null) $lang = "en";
if (($q->rowCount() <= 0) || ($q->rowCount() > 0 && ($resToken[0]["token"] == null || $resToken[0]["token"] == "" || !isset($resToken[0]["token"])))) {
    $salt = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["salt"];
    $_SESSION['talkToken'] = md5(uniqid($ident->username, true) . rand() . md5($salt));
    if ($q->rowCount() <= 0) {
        $q = $db->prepare('INSERT INTO user (name, token, lang, local_wikis, isGlobalAccess, isGlobal, userRole, rebind) VALUES (:name, :token, :lang, :local_wikis, :isGlobalAccess, :isGlobal, :userRole, 0)');
        $q->execute(array(':name' => $ident->username, ':token' => $_SESSION['talkToken'], ':userRole' => $userRole, ':lang' => $lang, ':local_wikis' => $_SESSION['projects'], ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
        $q = $db->prepare('INSERT INTO stats (user) VALUES (:user)');
        $q->execute(array(':user' => $ident->username));
    } else {
        $q = $db->prepare('UPDATE user SET token=:token, userRole=:userRole, local_wikis=:local_wikis, isGlobalAccess=:isGlobalAccess, isGlobal=:isGlobal, rebind=0 WHERE name=:name');
        $q->execute(array(':name' => $ident->username, ':token' => $_SESSION['talkToken'], ':userRole' => $userRole, ':local_wikis' => $_SESSION['projects'], ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
    }
} else {
    $_SESSION['talkToken'] = $resToken[0]["token"];
    if ($resToken[0]["lang"] === null || $resToken[0]["lang"] === "") {
        $q = $db->prepare('UPDATE user SET lang=:lang, userRole=:userRole, local_wikis=:local_wikis, isGlobalAccess=:isGlobalAccess, isGlobal=:isGlobal, rebind=0 WHERE name=:name');
        $q->execute(array(':name' => $ident->username, ':lang' => $lang, ':userRole' => $userRole, ':local_wikis' => $_SESSION['projects'], ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
    } else {
        $q = $db->prepare('UPDATE user SET local_wikis=:local_wikis, userRole=:userRole, isGlobalAccess=:isGlobalAccess, isGlobal=:isGlobal, rebind=0 WHERE name=:name');
        $q->execute(array(':name' => $ident->username, ':local_wikis' => $_SESSION['projects'], ':userRole' => $userRole, ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
   }
}
$q = $db->prepare('SELECT name FROM presets WHERE name=:name');
$q->execute(array(':name' => $ident->username));
if ($q->rowCount() <= 0) {
    $q = $db->prepare('INSERT INTO presets (name, preset) VALUES (:name, :preset)');
    $q->execute(array(':name' => $ident->username, ':preset' => 'Default'));
}
$db = null;

$projects = null;
if (isset($_SESSION['projects']) && $_SESSION['projects'] !== "")
    $projects = $_SESSION['projects'];
$_SESSION['userRole'] = $userRole;

$cookie_json = json_encode(["userName" => $ident->username, "tokenKey" => $accessToken->key, "tokenSecret" => $accessToken->secret, "talkToken" => $_SESSION['talkToken'], "mode" => $_SESSION['mode'], "accessGlobal" => $accessGlobal, "userRole" => $userRole, "projects" => $projects]);
setcookie("SWViewer-auth", $cookie_json, time() + 60 * 60 * 24 * 31, "/", "swviewer.toolforge.org", TRUE, TRUE);
session_write_close();

header("Location: https://swviewer.toolforge.org");
?>