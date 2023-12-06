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
$checkGR = false;
forEach ($globalInfo['query']['globaluserinfo']['groups'] as $globalGroup) {
    if ($globalGroup == 'steward' || $globalGroup == 'global-sysop' || $globalGroup == 'global-rollbacker') {
        $global = true;
        if ($globalGroup == 'steward') {
            $userRole = "S";
            $checkGR = true;
        }
        if ($globalGroup == 'global-sysop')
            $userRole = "GS";
        if ($globalGroup == 'global-rollbacker')
            $checkGR = true;
    }
}
$_SESSION['projects'] = "";
if ($global == true && $checkGR == false)
    $_SESSION['notGR'] = true;
else
    $_SESSION['notGR'] = false;

if ($global == true || $ident->username == "Ajbura")
    $_SESSION['mode'] = 'global';
else {
    $checkLocal = false;

    $patrollerGroup = ["bgwiki", "bnwikibooks", "dawiki", "enwikivoyage", "frwikisource", "frwiktionary", "hewiktionary", "hewiki", "hewikinews", "hewikibooks", "hrwiki", "itwikiversity", "itwikibooks", "itwikivoyage", "itwiktionary", "metawiki", "mkwiki", "nnwiki", "nowiki", "nowikibooks", "trwiki", "zhwikiversity", "zhwikivoyage"];
    $editorGroup = ["dewiki", "enwikibooks", "enwikinews", "elwikinews", "fawikinews", "huwiki", "kawiki", "plwiki", "plwikisource", "ptwikibooks", "trwikiquote", "zh_classicalwiki"];
    $eliminatorGroup = ["fawiki", "viwiki", "viwikibooks"];
    $botAdminGroup = ["ckbwiki", "frwiktionary", "mlwiki"];
    $testSysopGroup = ["incubatorwiki"];
    $wikidataStaffGroup = ["testwikidatawiki", "wikidatawiki"];
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

$flaggedRevsWikis = [
        'alswiki' => ['sysop', 'editor', 'reviewer'], 'arwiki' => ['sysop', 'editor', 'reviewer'], 'bewiki' => ['sysop', 'editor'],
        'bnwiki' => ['sysop', 'reviewer'], 'bswiki' => ['sysop', 'editor'],
        'cawikinews' => ['sysop', 'editor', 'reviewer'], 'cewiki' => ['sysop', 'editor', 'reviewer'],
        'ckbwiki' => ['sysop', 'reviewer'], 'dewiki' => ['sysop', 'editor', 'reviewer'],
        'dewikiquote' => ['sysop', 'editor', 'reviewer'], 'dewiktionary' => ['sysop', 'editor', 'reviewer'],
        'elwikinews' => ['sysop', 'editor'], 'enwiki' => ['sysop', 'reviewer'],
        'enwikibooks' => ['sysop', 'editor'], 'enwikinews' => ['sysop', 'editor'],
        'eowiki' => ['sysop', 'editor', 'reviewer'], 'eswikinews' => ['sysop', 'editor', 'reviewer'], 
        'fawiki' => ['sysop', 'patroller', 'eliminator', 'reviewer'], 'fawikinews' => ['sysop', 'editor'],
        'fiwiki' => ['sysop', 'editor', 'reviewer'], 'frwikinews' => ['sysop', 'facilitator'], 
        'hewikisource' => ['sysop', 'reviewer', 'editor'], 'hiwiki' => ['sysop', 'reviewer'], 
        'huwiki' => ['sysop', 'editor'], 'iawiki' => ['sysop', 'editor', 'reviewer'],
        'idwiki' => ['sysop', 'editor', 'reviewer'], 'iswiktionary' => ['sysop', 'editor', 'reviewer'],
        'kawiki' => ['sysop', 'editor', 'reviewer'], 'lawikisource' => ['sysop', 'editor', 'reviewer'],
        'mkwiki' => ['sysop', 'editor', 'reviewer'], 'plwiki' => ['sysop', 'editor', 'reviewer'],
        'plwikisource' => ['sysop', 'editor', 'reviewer'], 'plwiktionary' => ['sysop', 'editor', 'reviewer'],
        'ptwiki' => [], 'ptwikibooks' => ['sysop', 'editor', 'reviewer'],
        'ptwikinews' => ['sysop', 'editor', 'reviewer'], 'ruwiki' => ['editor'],
        'ruwikinews' => ['sysop', 'editor'], 'ruwikiquote' => ['sysop', 'editor', 'reviewer'],
        'ruwikisource' => ['sysop', 'editor'], 'ruwiktionary' => ['sysop', 'editor', 'reviewer'],
        'sqwiki' => ['sysop', 'editor', 'reviewer'], 'tawikinews' => ['sysop', 'editor', 'reviewer'],
        'trwiki' => ['sysop', 'patroller'], 'trwikiquote' => ['sysop', 'editor'],
        'ukwiki' => ['sysop', 'editor', 'reviewer'], 'ukwiktionary' => ['sysop', 'editor', 'reviewer'],
        'vecwiki' => ['sysop', 'editor', 'reviewer'], 'zh_classicalwiki' => ['sysop', 'editor', 'reviewer']];
$flagged = [];
forEach ($globalInfo['query']['globaluserinfo']['merged'] as $localGroups)
        if (array_key_exists('groups', $localGroups))
            if (array_key_exists($localGroups['wiki'], $flaggedRevsWikis))
                if (!empty(array_intersect($flaggedRevsWikis[$localGroups['wiki']], $localGroups['groups'])))
                    array_push($flagged, $localGroups['wiki']);
$flagged = implode (", ", $flagged);


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
        $q = $db->prepare('INSERT INTO user (name, token, lang, flaggedRevs, local_wikis, isGlobalAccess, isGlobal, userRole, rebind) VALUES (:name, :token, :lang, :flaggedRevs, :local_wikis, :isGlobalAccess, :isGlobal, :userRole, 0)');
        $q->execute(array(':name' => $ident->username, ':token' => $_SESSION['talkToken'], ':userRole' => $userRole, ':lang' => $lang, ':flaggedRevs' => $flagged, ':local_wikis' => $_SESSION['projects'], ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
        $q = $db->prepare('INSERT INTO stats (user) VALUES (:user)');
        $q->execute(array(':user' => $ident->username));
    } else {
        $q = $db->prepare('UPDATE user SET token=:token, userRole=:userRole, flaggedRevs=:flaggedRevs, local_wikis=:local_wikis, isGlobalAccess=:isGlobalAccess, isGlobal=:isGlobal, rebind=0 WHERE name=:name');
        $q->execute(array(':name' => $ident->username, ':token' => $_SESSION['talkToken'], ':userRole' => $userRole, ':flaggedRevs' => $flagged, ':local_wikis' => $_SESSION['projects'], ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
    }
} else {
    $_SESSION['talkToken'] = $resToken[0]["token"];
    if ($resToken[0]["lang"] === null || $resToken[0]["lang"] === "") {
        $q = $db->prepare('UPDATE user SET lang=:lang, userRole=:userRole, flaggedRevs=:flaggedRevs, local_wikis=:local_wikis, isGlobalAccess=:isGlobalAccess, isGlobal=:isGlobal, rebind=0 WHERE name=:name');
        $q->execute(array(':name' => $ident->username, ':lang' => $lang, ':userRole' => $userRole, ':flaggedRevs' => $flagged, ':local_wikis' => $_SESSION['projects'], ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
    } else {
        $q = $db->prepare('UPDATE user SET flaggedRevs=:flaggedRevs, local_wikis=:local_wikis, userRole=:userRole, isGlobalAccess=:isGlobalAccess, isGlobal=:isGlobal, rebind=0 WHERE name=:name');
        $q->execute(array(':name' => $ident->username, ':flaggedRevs' => $flagged, ':local_wikis' => $_SESSION['projects'], ':userRole' => $userRole, ':isGlobalAccess' => $accessGlobalSQL, ':isGlobal' => $isGlobal));
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

$cookie_json = json_encode(["userName" => $ident->username, "tokenKey" => $accessToken->key, "tokenSecret" => $accessToken->secret, "talkToken" => $_SESSION['talkToken'], "mode" => $_SESSION['mode'], "notGR" => $_SESSION['notGR'], "accessGlobal" => $accessGlobal, "userRole" => $userRole, "projects" => $projects]);
setcookie("SWViewer-auth", $cookie_json, time() + 60 * 60 * 24 * 31, "/", "swviewer.toolforge.org", TRUE, TRUE);
session_write_close();

header("Location: https://swviewer.toolforge.org");
