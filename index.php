<!DOCTYPE html>
<?php
header('Content-Type: text/html; charset=utf-8');
session_name( 'SWViewer' );
session_start();
# Redirect to https
if (!(isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' ||
        $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    session_write_close();
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}


?>
<html id="parentHTML" class="notranslate" lang="">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Permissions-Policy" content="interest-cohort=()"/>
    <title>SWViewer</title>

    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <meta name="application-name" content="SWViewer">
    <meta name="author" content="Iluvatar, Ajbura, 1997kB">
    <meta name="description" content="App for monitoring recent changes of Wikipedia in real-time.">
    <meta name="keywords" content="swmt, patrolling wikipedia, recent changes, ">
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
    <script type="text/javascript" src="//tools-static.wmflabs.org/cdnjs/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
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

    <style>
        .ltr-mark:after { content: "\200E"; }
        .rtl-mark:after { content: "\200F"; }
    </style>

    <script>
        function sandwichLocalisation(baseContent, dirLocal, localMessage, targetEl, patternType, parsedLen, styleEl, uniqId, linkLocalisation, baseAdd = false) {
            var parsedMessage; var baseContent = baseContent;
            if (patternType === 'link')
                parsedMessage = (dirLocal === 'ltr') ? localMessage.match(/^(.*?)\[\$link\|(.*?)\](.*)$/) : localMessage.match(/^(.*?)\[\$\s?link\s?\|\s?(.*?)\](.*)$/);
            else {
                if (patternType === 'name')
                    parsedMessage = (dirLocal === 'ltr') ? localMessage.match(/^(.*?)\$1(.*)/) : localMessage.match(/^(.*?)\$\s?1(.*)/);
                else
                    parsedMessage = (dirLocal === 'ltr') ? localMessage.match(/^(.*?)\[\$1\|(.*?)\](.*)$/) : localMessage.match(/^(.*?)\[\$\s?1\s?\|\s?(.*?)\](.*)$/);
            }
            if (parsedMessage !== null && parsedMessage.length === parsedLen) {
                targetEl.textContent = '';
                var preLocalisedEl1 = baseContent.createElement('div');
                var preLocalisedEl2 = (linkLocalisation === false) ? baseContent.createElement('div') : baseContent.createElement('a');
                var preLocalisedEl3 = baseContent.createElement('div');
                preLocalisedEl1.id = 'localisedEl' + uniqId + '1';
                preLocalisedEl2.id = 'localisedEl' + uniqId + '2';
                preLocalisedEl3.id = 'localisedEl' + uniqId + '3';
                preLocalisedEl1.style.display = preLocalisedEl2.style.display = preLocalisedEl3.style.display = styleEl;
                if (linkLocalisation !== false) {
                    preLocalisedEl2.href = linkLocalisation;
                    preLocalisedEl2.rel = 'noopener noreferrer';
                    preLocalisedEl2.target = '_blank';
                }

                targetEl.appendChild(preLocalisedEl1); targetEl.appendChild(preLocalisedEl2); targetEl.appendChild(preLocalisedEl3);
                if (baseAdd !== false)
                    baseContent = baseAdd;
                baseContent.getElementById('localisedEl' + uniqId + '1').textContent = parsedMessage[1];
                if (parsedLen === 3) {
                    baseContent.getElementById('localisedEl' + uniqId + '2').textContent = 'SWViewer';
                    baseContent.getElementById('localisedEl' + uniqId + '3').textContent = parsedMessage[2];
                } else {
                    baseContent.getElementById('localisedEl' + uniqId + '2').textContent = parsedMessage[2];
                    baseContent.getElementById('localisedEl' + uniqId + '3').textContent = parsedMessage[3];
                }
            }
        }
    </script>
</head>

<?php
# Callback errors
if (isset($_GET["error"])) {
    if ($_GET["error"] == "rights") echo "<div style='background-color: red;' align=center>Sorry, to use this application <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Special:MyLanguage/Rollback'>local</a> or <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Special:MyLanguage/Global_rollback'>global</a> rollback is required.<br>If you have rollback right and see that error, then report about it on <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/Special:MyLanguage/SWViewer'>talk page</a>. Thanks!</div>";
    if ($_GET["error"] == "internal") echo "<div style='background-color: red;' align=center>Internal server error</div>";
    session_write_close();
    exit();
}

# If user is not logged in, then show login layer
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
    echo "
<noscript>
    <span style='color: red;'>JavaScript is not enabled!</span>
</noscript>

<div id='login-page-base' class='login-base secondary-cont' style='display: none'>
    <div class='login-card'>
        <div style='text-align: center;'>
            <span class='fs-xl custom-lang' style='font-weight: bold;'>[login-welcome]</span>
            <a id='abtn' class='i-btn__accent accent-hover custom-lang' style='margin: 16px 0; color: var(--tc-accent) !important; padding: 0 24px; text-decoration: none !important;' href='https://swviewer.toolforge.org/php/oauth.php?action=auth'>[login-oauth]</a>
            <span id='login-r' class='fs-xs custom-lang' style='width: 80%'>[login-rights]</span>
            <span id='login-d' class='fs-xs' style='margin-top: 3px; width: 80%'><div id='ld1' style='display: inline'></div><div id='ld2' style='display: inline' onclick='openPO()'></div><div id='ld3' style='display: inline'></div></span>
        </div>
        <div>
            <span class='i-btn__secondary-outlined secondary-hover fs-md custom-lang' style='height: 35px; margin-bottom: 8px;' onclick='openPO();'>[about]</span>
            <span class='fs-xs'>Brought to you by <a rel='noopener noreferrer' target='_blank' href='https://meta.wikimedia.org/wiki/User:Iluvatar'>Iluvatar</a>, <a rel='noopener noreferrer' target='_blank' href='https://ajbura.github.io'>ajbura</a>, <a rel='noopener noreferrer' target='_blank' href='https://en.wikipedia.org/wiki/User:1997kB'>1997kB</a></span>
        </div>
    </div>
</div>

<!-- po Overlay-->
<div id='POOverlay' class='po__overlay' onclick='closePO()'></div>

<script>
    (async function() {
        var code = 'en';
        code = window.navigator.language || navigator.userLanguage;
        let responseLang = await fetch('i18n/en.json');
        const baseLang = await responseLang.json();

        let responseLangInfo = await fetch('php/localisation.php?mycode=' + code);
        language = await responseLangInfo.json();
        var useLang = []; useLang['@metadata'] = []; var dirLang = language['dir']; var languageIndex = language['code'];
        document.getElementById('parentHTML').setAttribute('dir', language['dir']);
        document.getElementById('parentHTML').setAttribute('lang', languageIndex);
        if (language['code'] === 'en') {
            for (m in baseLang) {
                useLang[m] = baseLang[m];
            }
            useLang['@metadata']['authors'] = baseLang['@metadata']['authors'];
            useLang['@metadata']['langName'] = 'English';
        } else {
            let responseLang2 = await fetch('i18n/' + language['code'] + '.json');
            const selectLang = await responseLang2.json();
            for (m in baseLang) {
                if (baseLang.hasOwnProperty(m)) {
                    if (m !== '@metadata') {
                        if (selectLang.hasOwnProperty(m)) {
                            if (selectLang[m] !== '' && selectLang[m] !== null) useLang[m] = selectLang[m];
                            else useLang[m] = baseLang[m]
                        } else
                            useLang[m] = baseLang[m];
                    }
                }
            }
            useLang['@metadata']['authors'] = selectLang['@metadata']['authors'];
            useLang['@metadata']['langName'] = language['name'];
        }
        var elementsLang = document.getElementsByClassName('custom-lang');
        for (el in elementsLang) {
            if (elementsLang.hasOwnProperty(el)) {
                var attrs = elementsLang[el].attributes;
                for (l in attrs) {
                    if (attrs.hasOwnProperty(l))
                        if (typeof attrs[l].value !== 'undefined')
                            if (useLang.hasOwnProperty(attrs[l].value.replace('[', '').replace(']', '')))
                                elementsLang[el].setAttribute(attrs[l].name, useLang[attrs[l].value.replace('[', '').replace(']', '')]);
                }
                if (typeof elementsLang[el].value !== 'undefined')
                    if (useLang.hasOwnProperty(elementsLang[el].value.replace('[', '').replace(']', '')))
                        elementsLang[el].value = useLang[elementsLang[el].value.replace('[', '').replace(']', '')];
                if (typeof elementsLang[el].textContent !== 'undefined')
                    if (useLang.hasOwnProperty(elementsLang[el].textContent.replace('[', '').replace(']', '')))
                        elementsLang[el].textContent = useLang[elementsLang[el].textContent.replace('[', '').replace(']', '')];
            }
        }

        const lr = useLang['login-rights']; var loginR = document.getElementById('login-r');
        const parserLr = (dirLang === 'ltr') ? lr.match(/^(.*?)\[\\$1\|(.*?)\](.*?)\[\\$2\|(.*?)\](.*)$/) : lr.match(/^(.*?)\[\\$\s?1\s?\|\s?(.*?)\](.*?)\[\\$\s?2\s?\|\s?(.*?)\](.*)$/);
        if (parserLr !== null && parserLr.length === 6) {
            loginR.textContent = '';
            var lrdiv1 = document.createElement('div'); var lrdiv2 = document.createElement('div'); var lrdiv3 = document.createElement('div');
            lrdiv1.id = 'lr1'; lrdiv2.id = 'lr2'; lrdiv3.id = 'lr3'; lrdiv1.style.display = lrdiv2.style.display = lrdiv3.style.display = 'inline';
            if (dirLang !== 'rtl') lrdiv1.style.marginRight = '1px'; else lrdiv3.style.marginRight = '1px';
            lrdiv2.style.marginRight = '1px';
            var lra1 = document.createElement('a'); var lra2 = document.createElement('a');
            lra1.id = 'lra1'; lra2.id = 'lra2'; lra1.style.display = lra2.style.display = 'inline'; lra1.style.marginRight = lra2.style.marginRight = '1px';
            lra1.href = 'https://meta.wikipedia.org/wiki/Special:MyLanguage/Rollback'; lra1.rel = 'noopener noreferrer'; lra1.target = '_blank';
            lra2.href = 'https://meta.wikimedia.org/wiki/Special:MyLanguage/Global_rollback'; lra2.rel = 'noopener noreferrer'; lra2.target = '_blank';
            loginR.appendChild(lrdiv1); loginR.appendChild(lra1); loginR.appendChild(lrdiv2); loginR.appendChild(lra2); loginR.appendChild(lrdiv3);
            document.getElementById('lr1').textContent = parserLr[1];
            document.getElementById('lra1').textContent = parserLr[2];
            document.getElementById('lr2').textContent = parserLr[3];
            document.getElementById('lra2').textContent = parserLr[4];
            document.getElementById('lr3').textContent = parserLr[5];
        }

        const ld = useLang['login-disclaimer']; var loginD = document.getElementById('login-d');
        const parserLd = (dirLang === 'ltr') ? ld.match(/^(.*?)\[\\$1\|(.*?)\](.*)$/) : ld.match(/^(.*?)\[\\$\s?1\s?\|\s?(.*?)\](.*)$/);
        if (parserLd === null || parserLd.length !== 4)
            loginD.innerHtml = '[login-disclaimer]';
        else {
            var ld1 = document.getElementById('ld1'); var ld2 = document.getElementById('ld2');  var ld3 = document.getElementById('ld3');

            if (dirLang !== 'rtl') ld1.style.marginRight = '3px'; ld2.style.marginRight = '3px';
            ld2.style.color = 'var(--link-color)'; ld2.style.textDecoration = 'none'; ld2.style.cursor = 'pointer';

            ld1.textContent = parserLd[1];
            ld2.textContent = parserLd[2];
            ld3.textContent = parserLd[3];
        }

        window.useLang = useLang; window.dirLang = dirLang; window.languageIndex = languageIndex;
        var lastOpenedPO = undefined;
        $.getScript('https://swviewer.toolforge.org/js/modules/about.js');
        document.getElementById('login-page-base').style.display = 'block';
    })();


    document.onkeydown = function (e) {
        if (!e) e = window.event;
        var keyCode = e.which || e.keyCode || e.key;
        if (keyCode === 27)
            if (document.getElementById('POOverlay').classList.contains('po__overlay__active'))
                closePO();
    };

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

$q = $db->prepare('SELECT name, lang, locked, rebind, betaTester FROM user WHERE name=:name');
$q->execute(array(':name' => $_SESSION["userName"]));
$result = $q->fetchAll();
$isLocked = intval($result[0]["locked"]);
$isBetaTester = intval($result[0]["betaTester"]);
$isRebind = intval($result[0]["rebind"]);

# User is banned or need refesh parameters
if ($isLocked !== 0 || $isRebind == 1) {
    $_SESSION = array();
    session_write_close();
    if (isset($_COOKIE['SWViewer-auth'])) {
       unset($_COOKIE['SWViewer-auth']);
       setcookie('SWViewer-auth', '', time() - 3600, '/');
    }
    if ($isLocked !== 0)
        echo "Access denied. You have been blocked.";
    else
        echo "<script>window.location.href = 'https://swviewer.toolforge.org/php/oauth.php?action=unlogin&kik=1';</script>";
    exit();
}


$isBetaTest = false;

# User is not beta tester
if ($isBetaTest === true && $isBetaTester === 0) {
    echo "Access denied. Please add yourself at <a href='https://meta.wikimedia.org/wiki/SWViewer/members' rel='noopener noreferrer' target='_blank'>beta tester list</a>, and let us know in <a href='http://ircredirect.toolforge.org/?server=irc.libera.chat&channel=swviewer&consent=yes' rel='noopener noreferrer' target='_blank'>IRC channel</a> or <a href='https://discord.gg/UTScYTR' rel='noopener noreferrer' target='_blank'>Discord server</a>.";
    $_SESSION = array();
    session_write_close();
    exit();
}

# Get dir writing to php var
$rtl = Array ("dv", "nqo", "syc", "arc", "yi", "ydd", "tmr", "lad-hebr", "he", "ur", "ug-arab", "skr-arab", "sdh", "sd", "ps", "prs", "pnb", "ota", "mzn", "ms-arab", "lrc", "luz", "lki", "ku-arab", "ks-arab", "kk-arab", "khw", "ha-arab", "glk", "fa", "ckb", "bqi", "bgn", "bft", "bcc", "azb", "az-arab", "arz", "ary", "arq", "ar", "aeb-arab");
$langDir = (in_array($result[0]["lang"], $rtl)) ? "rtl" : "ltr";

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

<body  class="full-screen" id="mainapp-body">

<!-- Loading UI -->
<div id="loading" class="secodnary-cont" style="padding: 16px; background: #ffffff; display: flex; align-items: center; justify-content: center; align-content: center; flex-wrap: wrap; position: fixed; z-index: 999;">
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
                    <div id="btn-home" class="tab__active primary-hover custom-lang" onclick="clickHome(); closePW();" aria-label="[tooltip-home]" i-tooltip="right">
                        <div class="tab-indicator"></div>
                        <img class="touch-ic primary-icon custom-lang" src="./img/swviewer-filled.svg" alt="[talk-img-app]">
                    </div>
                    <div id="btn-talk" class="primary-hover disabled custom-lang" onclick="openPW('talkForm')" aria-label="[tooltip-talk]" i-tooltip="right">
                        <div class="tab-indicator"></div>
                        <span id="badge-talk" class="tab-notice-indicator" style="display: none; background-color: var(--bc-positive);">{{numberLocale(users.length)}}</span>
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon custom-lang" src="./img/message-filled.svg" alt="[img-message]">
                    </div>
                    <div id="btn-logs" class="primary-hover custom-lang" onclick="openPW('logs')" aria-label="[tooltip-logs]" i-tooltip="right">
                        <div class="tab-indicator"></div>
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon custom-lang" src="./img/doc-filled.svg" alt="[img-logos]">
                    </div>
                    <div id="btn-unlogin" class="primary-hover custom-lang" onclick="logout(); closeSidebar();" aria-label="[tooltip-logout]" i-tooltip="right">
                        <img class="touch-ic primary-icon custom-lang" src="./img/power-filled.svg" alt="[img-logout]">
                    </div>
                    <div id="btn-about" class="primary-hover custom-lang" style="margin-top: auto;" onclick="openPO('about'); closeSidebar();" aria-label="[about]" i-tooltip="right">
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon custom-lang" src="./img/about-filled.svg" alt="[img-about]">
                    </div>
                    <div id="btn-notification" class="primary-hover custom-lang" onclick="openPO('notificationPanel'); closeSidebar(); notifyOpen();" aria-label="[tooltip-notification]" i-tooltip="right">
                        <span id="notify-indicator" class="tab-notice-indicator tab-notice-indicator__inactive" style="background-color: var(--bc-negative);">0</span>
                        <span class="loading-tab tab-notice-indicator">!</span>
                        <img class="touch-ic primary-icon custom-lang" src="./img/bell-filled.svg" alt="[img-notification]">
                    </div>
                    <div id="btn-settings" class="primary-hover custom-lang" onclick="openPO('settingsOverlay'); closeSidebar();" aria-label="[tooltip-settings]" i-tooltip="right">
                        <img class="touch-ic primary-icon custom-lang" src="./img/settings-filled.svg" alt="[img-settings]">
                    </div>
                </div>
            </div>
            <!-- Drawer -->
            <div id="queueDrawer" class="drawer-base primary-cont">
                <div class="edit-queue-base">
                    <div class="action-header eq__header">
                        <div class="mobile-only primary-hover custom-lang" onclick="openSidebar();" aria-label="[tooltip-m-sidebar]" i-tooltip="bottom-left">
                            <img class="touch-ic primary-icon custom-lang" src="./img/drawer-filled.svg" alt="[img-navigation]">
                        </div>
                        <span id="presetsArrow" class="presets-arrow action-header__title fs-lg disabled" onClick="togglePresets()">
                            <span id="drawerPresetTitle" class="drawer-preset-title custom-lang">[presets-default-title]</span>
                        </span>
                        <div id="editCurrentPreset" class="primary-hover disabled custom-lang" aria-label="[tooltip-edit-preset]" i-tooltip="bottom-right">
                            <img class="touch-ic primary-icon custom-lang" src="./img/filter-bar-filled.svg" alt="[img-edit]">
                        </div>
                        <div id="moreOptionBtnMobile" class="mobile-only primary-hover disabled custom-lang" onclick="toggleMoreControl();" aria-label="[tooltip-more-options]" i-tooltip="bottom-right">
                            <img class="touch-ic primary-icon custom-lang" src="./img/v-dots-filled.svg" alt="[img-options]">
                        </div>
                    </div>
                    <div id="presetBody" class="preset__body" style="height: 0;">
                        <div class="primary-scroll">
                            <div id="presetsBase" class="fs-md">
                                <button class="i-btn__primary primary-hover fs-sm" style="background-color: var(--bc-primary-hover);" onclick="editPreset();">
                                    <img class="touch-ic primary-icon custom-lang" src="./img/plus-filled.svg" alt="[img-plus]"><span class="custom-lang">[presets-button-create]</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="eqBody" class="eq__body">
                        <div class="queue-base primary-scroll">
                            <div class="queue" id="queue">
                                <div class="talk-svg" style="display: none; cursor: default;">
                                    <span class="fs-md custom-lang">[queue-empty-msg]</span>
                                </div>
                                <div class="primary-hover"  ng-click="select(edit)" ng-repeat="edit in edits track by $index">
                                    <div class="queue-col">
                                        <div class="queue-ores" style="background-color: {{edit.ores.color}}">{{edit.ores.score}}</div>
                                        <div class="queue-new">{{edit.isNew}}</div>
                                    </div>
                                    <div class="queue-row">
                                        <div class="queue-wikiname fs-sm" ng-style="editColor(edit)">
                                            {{edit.wiki}}&#x200E;
                                            <span class="fs-xs" ng-style="byteCountColor(edit.byteCount)">({{edit.byteCount}}&#x200E;)</span>
                                        </div>
                                        <div class="queue-title fs-xs">{{edit.title}}</div>
                                        <div class="queue-username fs-xs">{{edit.user}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Status Bar -->
            <div id="statusbar" class="statusbar-base primary-cont">
                <div class="statusbar-left-cont">
                    <div ng-click="pause()" class="status__notify primary-hover">
                        <div id="recentStreamIndicator" class="recentStream__indicator"></div>
                        <span>{{recentChangeStatus.status}}</span>
                    </div>
                    <div class="status__notify primary-hover">
                        <img class="touch-ic primary-icon" src="./img/eye-filled.svg">
                        <span>{{numberLocale(sessionActions.diffViewed)}}</span>
                    </div>
                    <div class="status__notify primary-hover" style="padding-right: 4px;" onclick="searchMyLogs(1);">
                        <img class="touch-ic primary-icon" src="./img/rollback-filled.svg">
                        <span>{{numberLocale(sessionActions.rollback)}}</span>
                    </div>
                    <div class="status__notify" style="padding: 0;">
                        <span style="font-weight: bold;">&#183;</span>
                    </div>
                    <div class="status__notify primary-hover" style="padding-left: 4px;" onclick="searchMyLogs(2);">
                        <span>{{numberLocale(sessionActions.undo)}}</span>
                    </div>
                    <div class="status__notify primary-hover" onclick="searchMyLogs(3);">
                        <img class="touch-ic primary-icon" src="./img/tag-filled.svg">
                        <span>{{numberLocale(sessionActions.delete)}}</span>
                    </div>
                    <div class="status__notify primary-hover" onclick="searchMyLogs(4);">
                        <img class="touch-ic primary-icon" src="./img/pencil-filled.svg">
                        <span>{{numberLocale(sessionActions.edit)}}</span>
                    </div>
                    <div class="status__notify primary-hover" onclick="searchMyLogs(5);">
                        <img class="touch-ic primary-icon" src="./img/warning-filled.svg">
                        <span>{{numberLocale(sessionActions.warn)}}</span>
                    </div>
                    <div class="status__notify primary-hover" onclick="searchMyLogs(6);">
                        <img class="touch-ic primary-icon" src="./img/report-filled.svg">
                        <span>{{numberLocale(sessionActions.report)}}</span>
                    </div>
                </div>
                <div class="statusbar-right-cont">
                    <div class='status__notify primary-hover'>
                        <span id="statusbarTime" onclick="changeTimeFormat(false);">Time</span>
                    </div>
                </div>
            </div>
            <!-- Main Window -->
            <div class="window-base secondary-cont">
                <div id="windowContent" class="window-content">
                    <!-- description container -->
                    <div id="description-container" class="description-container fs-md" style="display: none; margin-top: 0;">
                        <div class="desc-un">
                            <div id="us" class="fs-sm custom-lang"><span class="custom-lang">[diff-info-user]</span>&nbsp;<div id="userLinkSpec" ng-click="openLink('diff');"></div></div>
                            <div class="fs-sm"><span class="custom-lang">[diff-info-namespace]</span>&nbsp;<div id="ns" style="display: inline-block" class="fs-sm"></div></div>
                        </div>
                        <div class="desc-wt">
                            <div class="fs-sm"><span class="custom-lang">[diff-info-wiki]</span>&nbsp;<div id="wiki" style="display: inline-block" class="fs-sm"></div></div>
                            <div id="tit" class="fs-sm" style="overflow: unset;"><span class="custom-lang">[diff-info-title]</span>&nbsp;<div id="pageLinkSpec" style="cursor: pointer; display: inline-block; color: var(--link-color);" ng-click="openLink('page');"></div></div>
                        </div>
                        <div class="desc-c">
                            <div class="fs-sm"><span class="custom-lang">[diff-info-comment]</span>&nbsp;<div id="com" style="display: inline-block" class="fs-sm"></div></div>
                        </div>
                    </div>
                    <!-- Mobile next diff button -->
                    <div id="drawerFab" class="drawer-fab mobile-only">
                        <div id="next-diff" class="accent-hover custom-lang" ng-click='nextDiff()' aria-label="[tooltip-m-next-difference]" i-tooltip="top-right">
                            <img class="touch-ic accent-icon custom-lang" src="./img/swviewer-filled.svg" alt="[img-next]">
                        </div>
                        <span id="next-diff-title" class="fs-md custom-lang">[diff-mo-fetching]</span>
                        <div class="accent-hover custom-lang" style="position: relative;" onclick="toggleMDrawer();" aria-label="[tooltip-m-queue]" i-tooltip="top-right">
                            <span class="drawer-btn__edits-count">{{edits.length}}</span>
                            <img class="touch-ic accent-icon custom-lang" src="./img/drawer-filled.svg" alt="[img-drawer]">
                        </div>
                    </div>
                    <div id="notificationFabBase" class="notification-fab-base notification-fab-base__inactive drawer-fab mobile-only">
                        <div id="notificationFab" class="secondary-hover custom-lang" onclick="openPO('notificationPanel'); notifyOpen();" aria-label="[tooltip-m-notification]" i-tooltip="top-left">
                            <span id="notify-fab-indicator" class="tab-notice-indicator" style="background-color: var(--bc-negative);">0</span>
                            <img class="secondary-icon touch-ic custom-lang" src="/img/bell-filled.svg" alt="[img-bell]">
                        </div>
                    </div>
                    <!-- Controls -->
                    <div id="moreControlOverlay" class="more-control__overlay"  onclick="closeMoreControl();"></div>
                    <div id="controlsBase" class="controls-base floatbar"  style="display: none;">
                        <!-- More control -->
                        <div id="moreControl" class="more-control more-control__hidden secondary-scroll">
                            <div>
                                <a class="secondary-hover fs-sm custom-lang" href='https://meta.wikimedia.org/wiki/Special:MyLanguage/Meta:Requests_for_help_from_a_sysop_or_bureaucrat' rel='noopener noreferrer' target='_blank'>[diff-mo-rfh]</a>
                                <span vr-line="secondary"></span>
                                <a class="secondary-hover fs-sm custom-lang" href='https://meta.wikimedia.org/wiki/Special:MyLanguage/Steward_requests/Miscellaneous' rel='noopener noreferrer' target='_blank'>[diff-mo-srm]</a>
                                <span vr-line="secondary"></span>
                                <a class="secondary-hover fs-sm custom-lang" href='https://meta.wikimedia.org/wiki/Special:MyLanguage/Steward_requests/Global' rel='noopener noreferrer' target='_blank'>[diff-mo-srg]</a>
                                <span vr-line="secondary"></span>
                                <a class="secondary-hover fs-sm custom-lang" href='https://meta.wikimedia.org/wiki/Special:MyLanguage/Global_sysops/Requests' rel='noopener noreferrer' target='_blank'>[diff-mo-gsr]</a>
                            </div>
                            <div id="CAUTH">
                                <div class="secondary-hover custom-lang" ng-click="copyCentralAuth()" aria-label="[tooltip-copy-link]" i-tooltip="top-left"><img class="touch-ic secondary-icon custom-lang" src="./img/copy-filled.svg" alt="[img-copy]"></div>
                                <a class="secondary-hover fs-md custom-lang" href='https://meta.wikimedia.org/wiki/Special:CentralAuth?target={{selectedEdit.user}}' onclick="closeMoreControl();" rel='noopener noreferrer' target='_blank'>[diff-mo-ca]</a>
                            </div>
                            <div>
                                <div class="secondary-hover custom-lang" ng-click="copyGlobalContribs()" aria-label="[tooltip-copy-link]" i-tooltip="top-left"><img class="touch-ic secondary-icon custom-lang" src="./img/copy-filled.svg" alt="[img-copy]"></div>
                                <a id="luxo" class="secondary-hover fs-md custom-lang" href='https://guc.toolforge.org/?src=hr&by=date&user={{selectedEdit.user}}' onclick="closeMoreControl();" rel='noopener noreferrer' target='_blank'>[diff-mo-guc]</a>
                            </div>
                            <div>
                                <div class="secondary-hover custom-lang" ng-click="copyViewHistory()" aria-label="[tooltip-copy-link]" i-tooltip="top-left"><img class="touch-ic secondary-icon custom-lang" src="./img/copy-filled.svg" alt="[img-copy]"></div>
                                <a class="secondary-hover fs-md custom-lang" href='{{selectedEdit.server_url + "" + selectedEdit.script_path}}/index.php?title={{selectedEdit.title}}&action=history' onclick="toggleMoreControl();" rel='noopener noreferrer' target='_blank'>[diff-mo-vh]</a>
                            </div>
                            <div >
                                <div id="editBtn" class="secondary-hover custom-lang" ng-click="openEditSource();" onclick="openPW('editForm'); closeMoreControl();" aria-label="[tooltip-edit-source]" i-tooltip="top-left">
                                    <img class="touch-ic secondary-icon custom-lang" src="./img/pencil-filled.svg" alt="[img-edit]">
                                </div>
                                <a class="secondary-hover fs-md custom-lang" ng-click="openEditSource();" onclick="openPW('editForm'); closeMoreControl();"><span style="color: var(--tc-secondary);">[diff-mo-es]</span></a>
                            </div>
                        </div>
                        <!-- Control buttons -->
                        <div id="control" class="toolbar">
                            <div class="desktop-only secondary-hover custom-lang" onclick="toggleMoreControl();" aria-label="[tooltip-more-options]" i-tooltip="top-right">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/v-dots-filled.svg" alt="[more-options]">
                            </div>
                            <div id="browser" class="secondary-hover custom-lang" ng-click="browser();" aria-label="[tooltip-open-browser]" i-tooltip="top-right">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/open-newtab-filled.svg" alt="[img-browser]">
                            </div>
                            <div id="tagBtn" class="secondary-hover custom-lang" ng-click="openTagPanel();" onclick="openPW('tagPanel')" aria-label="[tooltip-speedy-del]" i-tooltip="top">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/tag-filled.svg" alt="[img-edit]">
                            </div>
                            <div id="customRevertBtn" class="secondary-hover custom-lang" ng-click="openCustomRevertPanel();" aria-label="[tooltip-custom-rollback]" i-tooltip="top">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/custom-rollback-filled.svg" alt="[img-custom-rb]">
                            </div>
                            <div id="revert" class="secondary-hover custom-lang" ng-click="doRevert();" aria-label="[tooltip-rollback]" i-tooltip="top">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/rollback-filled.svg" alt="[img-rollback]">
                            </div>
                            <div id="back" class="secondary-hover custom-lang" ng-click="Back();" aria-label="[tooltip-last-diff]" i-tooltip="top-left">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/arrow-left-filled.svg" alt="[img-back]">
                            </div>
                        </div>
                    </div>
                    <!-- Welcome page and Difference viewer -->
                    <div class="diff-container frame-diff">
                        <iframe id='page-welcome' class='full-screen custom-lang' style='display: block;' title='[welcome-page-title]' src='templates/welcome.html'></iframe>
                        <iframe id='page' class='full-screen custom-lang' style='display: none;' title='[page-title]' sandbox='allow-same-origin allow-scripts'></iframe>
                    </div>

                    <!-- Edit Source | popup-window -->
                    <div id="editForm" class="pw__base" style='display: none; grid-template-areas: "pw__header pw__header" "pw__content pw__content";'>
                        <!--pw Header-->
                        <div class="pw__header action-header">
                            <div class="mobile-only secondary-hover custom-lang" onclick="openSidebar();" aria-label="[tooltip-m-sidebar]" i-tooltip="bottom-left">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/drawer-filled.svg" alt="[img-box]">
                            </div>
                            <span class="action-header__title fs-xl custom-lang">[edit-source-title]</span>
                            <div class="mobile-only secondary-hover custom-lang" onclick="closePW()" aria-label="[tooltip-po-close]" i-tooltip="bottom-right">
                                <img class="touch-ic secondary-icon custom-lang" src="./img/cross-filled.svg" alt="[talk-img-cross]">
                            </div>
                            <span class="desktop-only pw__esc secondary-hover fs-md" onclick="closePW()">esc</span>
                        </div>
                        <!--pw Content-->
                        <div id="editFormBody" class="pw__content">
                            <img id="editSourceLoadingAnim" class="secondary-icon touch-ic" src="/img/swviewer-droping-anim.svg" style="opacity: .4; width: 100px; height: 100px; margin: auto;">
                            <textarea id="textpage" class="pw__content-body secondary-scroll editForm__textarea fs-md custom-lang" style="padding-bottom: 40px; color: var(--tc-secondary-low);" title="[tooltip-edit-form]"></textarea>

                            <div class="pw__floatbar">
                                <form ng-submit="saveEdit()"><input id="summaryedit" class="secondary-placeholder fs-md custom-lang" title="[summary-title]" placeholder="[summary-placeholder]"></form>
                                <span vr-line></span>
                                <div id="editForm-save" class="secondary-hover custom-lang" ng-click="saveEdit()" aria-label="[tooltip-publish-changes]" i-tooltip="top-right">
                                    <img class="touch-ic secondary-icon custom-lang" src="./img/save-filled.svg" alt="[img-save]">
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
            <span class="action-header__title fs-lg custom-lang">[custom-revert-title]</span>
            <div class="mobile-only secondary-hover custom-lang" onclick="closePO()" aria-label="[tooltip-po-close]" i-tooltip="bottom-right">
                <img class="touch-ic secondary-icon custom-lang" src="./img/cross-filled.svg" alt="[talk-img-cross]">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <form id="summariesContainer" style="display: flex" ng-submit="doRevert();">
                    <input class="i-input__secondary secondary-placeholder fs-md custom-lang" style="margin-right: 8px; flex: 1;" title="[tooltip-reason]" name="credit" id="credit" placeholder="[custom-revert-placeholder]"/>
                    <button type="button" class="i-btn__accent accent-hover fs-md custom-lang" id="btn-cr-u-apply" ng-click="doRevert();">[custom-revert-button]</button>
                </form>
                <br>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[warn-user-title]</div>
                    <div class="i__description fs-xs custom-lang">[warn-user-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="warn-box" class="t-btn__secondary"></div>
                    </div>
                </div>
                <div class="i__base" id="last-warn-box">
                    <div class="i__title fs-md custom-lang">[max-warn-title]</div>
                    <div class="i__description fs-xs custom-lang">[max-warn-desc]</div>
                    <div class="i__content fs-sm">
                        <span id="max" class="i-checkbox" onclick="toggleICheckBox(this);"></span>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[treat-undo-title]</div>
                    <div class="i__description fs-xs custom-lang">[treat-undo-desc]</div>
                    <div class="i__content fs-sm">
                        <span id="treatUndo" class="i-checkbox" onclick="toggleICheckBox(this);"></span>
                    </div>
                </div>
                <label class="fs-md custom-lang">[common-summaries]</label>
                <div class="panel-cr-reasons" ng-repeat="description in selectedEdit.config.rollback track by $index">
                    <div class="fs-sm" ng-style="descriptionColor(description)" ng-click="selectRollbackDescription(description)">{{description.name}}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- tagPanel | Popup-overlay -->
    <div id="tagPanel" class="po__base">
        <div class="po__header action-header">
            <span class="action-header__title fs-lg custom-lang">[tag-deletion-title]</span>
            <div class="mobile-only secondary-hover custom-lang" onclick="closePO()" aria-label="[tooltip-po-close]" i-tooltip="bottom-right">
                <img class="touch-ic secondary-icon custom-lang" src="./img/cross-filled.svg" alt="[talk-img-cross]">
            </div>
            <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
        </div>
        <div class="po__content">
            <div class="po__content-body secondary-scroll">
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[warn-user-title]</div>
                    <div class="i__description fs-xs custom-lang">[warn-user-desc]</div>
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
                    <div id="GSRRole" class="i__title fs-md custom-lang" style="display: none">[gsr-add]</div>
                    <div id="addToGSR-description" class="i__description fs-xs"></div>
                    <div id="GSRRole2" class="i__content fs-sm" style="display: none">
                        <span id="addToGSR" class="i-checkbox" onclick="toggleICheckBox (this);"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Settings | Popup-overlay -->
        <div id="settingsOverlay" class="po__base">
            <div class="po__header action-header">
                <span class="action-header__title fs-lg custom-lang">[settings-title]</span>
                <div class="mobile-only secondary-hover custom-lang" onclick="closePO()" aria-label="[tooltip-po-close]" i-tooltip="bottom-right">
                    <img class="touch-ic secondary-icon custom-lang" src="./img/cross-filled.svg" alt="[talk-img-cross]">
                </div>
                <span class="desktop-only po__esc secondary-hover fs-md" onclick="closePO()">esc</span>
            </div>
            <div class="po__content">
                <div class="po__content-body secondary-scroll">
                    <div id="settingsBase">
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-theme]</div>
                            <div class="i__description fs-xs custom-lang">[settings-theme-descr]</div>
                            <div class="i__content fs-sm">
                                <select id="themeSelector" class="i-select__secondary fs-md"></select>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-language]</div>
                            <div class="i__description fs-xs custom-lang">[settings-language-descr]</div>
                            <div class="i__content fs-sm">
                                <select id="languageSelector" class="i-select__secondary fs-md" onchange="changeLanguageSelector()"></select>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-language-region]</div>
                            <div class="i__description fs-xs custom-lang">[settings-language-region-descr]</div>
                            <div class="i__content fs-sm">
                                <select id="localeSelector" class="i-select__secondary fs-md" onchange="changeLocaleSelector()"></select>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-sound]</div>
                            <div class="i__description fs-xs custom-lang">[settings-sound-descr]</div>
                            <div class="i__content fs-sm">
                                <select id="soundSelector" class="i-select__secondary fs-md">
                                    <option class="custom-lang" value="0">[settings-sound-none]</option>
                                    <option class="custom-lang" value="1">[settings-sound-all]</option>
                                    <option class="custom-lang" value="2">[settings-sound-msg-a-mentions]</option>
                                    <option class="custom-lang" value="3">[settings-sound-only-mentions]</option>
                                    <option class="custom-lang" value="4">[settings-sound-edits]</option>
                                    <option class="custom-lang" value="5">[settings-sound-only-edits]</option>
                                </select>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-revisions]</div>
                            <div class="i__description fs-xs custom-lang">[settings-revisions-descr]</div>
                            <div class="i__content fs-sm">
                                <select id="checkSelector" class="i-select__secondary fs-md">
                                    <option class="custom-lang" value="0">[settings-revisions-onlylast]</option>
                                    <option class="custom-lang" value="1">[settings-revisions-alert]</option>
                                    <option class="custom-lang" value="2">[settings-revisions-all]</option>
                                </select>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-direction]</div>
                            <div class="i__description fs-xs custom-lang">[settings-direction-descr]</div>
                            <div class="i__content fs-sm">
                                <div id="bottom-up-btn" class="t-btn__secondary" onclick="toggleTButton(this); bottomUp(this);"></div>
                            </div>
                        </div>
                        <div class="desktop-only i__base">
                            <div class="i__title fs-md custom-lang">[settings-rh-mode]</div>
                            <div class="i__description fs-xs custom-lang">[settings-rh-mode-descr]</div>
                            <div class="i__content fs-sm">
                                <div id="RH-mode-btn" class="t-btn__secondary" onclick="toggleTButton(this); RHModeBtn(this, false);"></div>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-terminate-stream]</div>
                            <div class="i__description fs-xs custom-lang">[settings-terminate-stream-descr]</div>
                            <div class="i__content fs-sm">
                                <div id="terminate-stream-btn" class="t-btn__secondary" onclick="toggleTButton(this); terminateStreamBtn(this, false);"></div>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-jumps]</div>
                            <div class="i__description fs-xs custom-lang" id="jumps-descr">[settings-jumps-descr]</div>
                            <div class="i__content fs-sm">
                                <div id="jumps-btn" class="t-btn__secondary" onclick="toggleTButton(this); jumpsState(this, false);"></div>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-hotkeys]</div>
                            <div class="i__description fs-xs custom-lang" id="hotkeys-descr">[settings-hotkeys-descr]</div>
                            <div class="i__content fs-sm">
                                <div id="hotkeys-btn" class="t-btn__secondary" onclick="toggleTButton(this); hotkeysState(this, false);"></div>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-limit]</div>
                            <div class="i__description fs-xs custom-lang">[settings-limit-descr]</div>
                            <div class="i__content fs-sm">
                                <input id="max-queue" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="max-queue" placeholder="[settings-limit-placeholder]">
                            </div>
                        </div>
                        <div id="control-panel" class="i__base" style="display: none">
                            <div class="i__title fs-md custom-lang">[settings-control]</div>
                            <div class="i__extra">
                                <ul class="i-chip-list fs-sm">
                                    <li><a id="cpLink" class="fs-sm custom-lang" href="https://swviewer.toolforge.org/php/control.php" rel="noopener noreferrer" target="_blank">[settings-control-panel]</a></li>
                                    <li><a id="saLink" class="fs-sm custom-lang" href="https://swviewer-service.toolforge.org" rel="noopener noreferrer" target="_blank">[settings-service]</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="i__base">
                            <div class="i__title fs-md custom-lang">[settings-beta]</div>
                            <div class="i__extra">
                                <ul class="i-chip-list fs-sm">
                                    <li><a class="fs-sm custom-lang" href="https://swviewer.toolforge.org/beta.php" rel="noopener noreferrer" target="_blank">[settings-beta-tester]</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- po Overlay-->
            <div id="POOverlay" class="po__overlay" onclick="closePO()"></div>

            <!-- Edit preset | Template -->
            <template id="editPTitleTemplate">
                <div>
                    <span class="fs-sm custom-lang">[presets-title]</span>
                    <input id="presetTitleInput" class="i-input__secondary secondary-placeholder fs-md" type="text" autocomplete="off" placeholder="">
                </div><br/>
            </template>
            <template id="editPresetTemplate">
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-registered]</div>
                    <div class="i__description fs-xs custom-lang">[presets-registered-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="registered-btn" class="t-btn__secondary" onclick="toggleTButton(this); registeredBtn(this);"></div>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-anons]</div>
                    <div class="i__description fs-xs custom-lang">[presets-anons-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="onlyanons-btn" class="t-btn__secondary" onclick="toggleTButton(this); onlyAnonsBtn(this);"></div>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-new]</div>
                    <div class="i__description fs-xs custom-lang">[presets-new-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="new-pages-btn" class="t-btn__secondary" onclick="toggleTButton(this); newPagesBtn(this);"></div>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-only-new]</div>
                    <div class="i__description fs-xs custom-lang">[presets-only-new-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="onlynew-pages-btn" class="t-btn__secondary" onclick="toggleTButton(this); onlyNewPagesBtn(this);"></div>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-edits-limit]</div>
                    <div class="i__description fs-xs custom-lang">[presets-edits-limit-desc]</div>
                    <div class="i__content fs-sm">
                        <input id="max-edits" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="max-edits" placeholder="[presets-edits-limit-placeholder]">
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-days-limit]</div>
                    <div class="i__description fs-xs custom-lang">[presets-days-limit-desc]</div>
                    <div class="i__content fs-sm">
                        <input id="max-days" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="max-days" placeholder="[presets-days-limit-placeholder]">
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-ns]</div>
                    <div class="i__description fs-xs" style="display:flex"><span id="ns-desc" class="custom-lang">[presets-ns-desc]</span></div>
                    <div class="i__content fs-sm">
                        <div id="btn-delete-ns" class="i-minus fs-sm" onclick="nsDeleteFunct()">-</div>
                        <input id="ns-input" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="" placeholder="[presets-enter-placeholder]">
                        <div id="btn-add-ns" class="i-plus fs-sm" onclick="nsAddFunct()">+</div>
                    </div>
                    <div class="i__extra">
                        <ul id="nsList" class="i-chip-list fs-sm"></ul>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-ores-filter]</div>
                    <div class="i__description fs-xs custom-lang">[presets-ores-filter-desc]</div>
                    <div class="i__content fs-sm">
                        <input id="ores-filter" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="ores-filter" placeholder="0-100">
                    </div>
                </div>
                <div id="sw-set" class="i__base" style="display:none;">
                    <div class="i__title fs-md custom-lang">[presets-sw]</div>
                    <div class="i__description fs-xs custom-lang">[presets-sw-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="small-wikis-btn" class="t-btn__secondary" onclick="toggleTButton(this); smallWikisBtn(this);"></div>
                    </div>
                </div>
                <div id="ad-set" class="i__base" style="display:none;">
                    <div class="i__title fs-md custom-lang">[presets-additional]</div>
                    <div class="i__description fs-xs" style="display:flex"><span id="adw" class="custom-lang">[presets-additional-desc]</span></div>
                    <div class="i__content fs-sm">
                        <div id="lt-300-btn" class="t-btn__secondary" onclick="toggleTButton(this); lt300Btn(this);"></div>
                    </div>
                </div>
                <div id="custom-set" class="i__base" style="display:none;">
                    <div class="i__title fs-md custom-lang">[presets-custom]</div>
                    <div class="i__description fs-xs custom-lang">[presets-custom-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="btn-bl-p-delete" class="i-minus fs-sm" onclick="blpDeleteFunct()">-</div>
                        <input id="bl-p" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="bl-p" placeholder="[presets-enter-placeholder]">
                        <div id="btn-bl-p-add" class="i-plus fs-sm" onclick="blpAddFunct()">+</div>
                    </div>
                    <div class="i__extra">
                        <ul id="blareap" class="i-chip-list fs-sm"></ul>
                    </div>
                </div>
                <div id="lang-set" class="i__base" style="display:none;">
                    <div class="i__title fs-md custom-lang">[presets-langset]</div>
                    <div class="i__description fs-xs custom-lang">[presets-langset-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="btn-l-p-delete" class="i-minus fs-sm" onclick="lDeleteFunct()">-</div>
                        <input id="l-p" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="l-p" placeholder="[presets-enter-placeholder]">
                        <div id="btn-l-p-add" class="i-plus fs-sm" onclick="lAddFunct()">+</div>
                    </div>
                    <div class="i__extra">
                        <ul id="lareap" class="i-chip-list fs-sm"></ul>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-wikis-wl]</div>
                    <div class="i__description fs-xs custom-lang">[presets-wikis-wl-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="btn-wl-p-delete" class="i-minus fs-sm" onclick="wlpDeleteFunct()">-</div>
                        <input id="wladdp" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="wladdp" placeholder="[presets-enter-placeholder]">
                        <div id="btn-wl-p-add" class="i-plus fs-sm" onclick="wlpAddFunct()">+</div>
                    </div>
                    <div class="i__extra">
                        <ul id="wlareap" class="i-chip-list fs-sm"></ul>
                    </div>
                </div>
                <div class="i__base">
                    <div class="i__title fs-md custom-lang">[presets-users-wl]</div>
                    <div class="i__description fs-xs custom-lang">[presets-users-wl-desc]</div>
                    <div class="i__content fs-sm">
                        <div id="btn-wl-u-delete" class="i-minus fs-sm" onclick="wluDeleteFunct()">-</div>
                        <input id="wladdu" class="i-input__secondary secondary-placeholder fs-sm custom-lang" name="wladdu" placeholder="[presets-enter-placeholder]">
                        <div id="btn-wl-u-add" class="i-plus fs-sm" onclick="wluAddFunct()">+</div>
                    </div>
                    <div class="i__extra">
                        <ul id="wlareau" class="i-chip-list fs-sm"></ul>
                    </div>
                </div>


            </template>

            <script src="js/index-noncritical.js" defer></script>
            <script src="js/modules/dialog.js" defer></script>
            <script src="js/modules/presets.js" defer></script>
            <script src="js/modules/swipe.js" defer></script>

            <!-- Scripts -->
            <script>
                document.getElementById('loadingBar').style.width = "50%";
                var diffstart, diffend, newstart, newend, startstring, endstring, config, dirLang, languageIndex;
                var useLang = []; useLang["@metadata"] = [];
                var activeSysops = [];
                var vandals = [];
                var suspects = [];
                var offlineUsers = [];
                var defaultWarnList = [];
                var defaultDeleteList = [];
                var countqueue = 0;
                var checkMode = 0;
                var hotkeys = 0;
                var jumps = 0;
                var sound = 0;
                var newSound;
                var terminateStream = 0;
                var messageSound;
                var privateMessageSound;
                var firstClick = false;
                var firstClickEdit = false;
                var preSettings = {};
                // presets value here is temp until we refill it from database.
                var presets = [{ title: "", regdays: "5", editscount: "100", anons: "1", registered: "1", new: "1", onlynew: "0", swmt: "0", users: "0", namespaces: "", wlusers: "", wlprojects: "", wikilangs: "", blprojects: ""}];
                var selectedPreset = 0;
                var themeIndex = undefined;
                const THEME_FIX = { '--bc-positive': 'rgb(36, 164, 100)', '--bc-negative': 'rgb(251, 47, 47)', '--ic-accent': 'invert(0.85) sepia(1) saturate(0) hue-rotate(200deg)', '--tc-accent': 'rgba(255, 255, 255, 1)', '--link-color': '#337ab7', '--tc-positive': 'var(--bc-positive)', '--tc-negative': 'var(--bc-negative)', '--fs-xl': '26px', '--fs-lg': '18px', '--fs-md': '16px', '--fs-sm': '14px', '--fs-xs': '11px', '--lh-xl': '1.5', '--lh-lg': '1.5', '--lh-md': '1.5', '--lh-sm': '1.5', '--lh-xs': '1.5', };
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
                    "System default": { },
                };

                document.getElementById("mainapp-body").onclick = function() {
                    if (firstClick === false) {
                        firstClick = true;
                        messageSound = new Audio("sounds/message.mp3");
                        privateMessageSound = new Audio("sounds/privateMessage.mp3");
                        newSound = new Audio("sounds/bump.wav");
                        messageSound.load();
                        privateMessageSound.load();
                        newSound.load();
                    }
                };

                var xhr = new XMLHttpRequest();
                xhr.open('GET', "php/settings.php?action=get&query=all", false);
                xhr.send();
                if (xhr.responseText === "Invalid request") location.reload(); // Bug with session on Safari browser
                var settingslist  = xhr.responseText;
                settingslist = JSON.parse(settingslist);

                var isGlobal = (settingslist['isGlobal'] !== null && settingslist['isGlobal'] !== "" && settingslist['isGlobal'] !== false && settingslist['isGlobal'] !== "0");
                var isGlobalModeAccess = (settingslist['isGlobalAccess'] !== null && settingslist['isGlobalAccess'] !== "" && settingslist['isGlobalAccess'] !== false && settingslist['isGlobalAccess'] !== "0");

                var userRole = (settingslist['userRole'] !== null && settingslist['userRole'] !== "") ? settingslist['userRole'] : "none";
                var userSelf = settingslist['userName'];
                // DO NOT GIVE TO ANYONE THIS TOKEN, OTHERWISE THE ATTACKER WILL CAN OPERATE AND SENDS MESSAGES UNDER YOUR NAME!
                var talktoken = settingslist['talkToken'];
                var local_wikis = (settingslist['local_wikis'] !== null && settingslist['local_wikis'] !== "") ? settingslist['local_wikis'].split(',') : [];

                if (userSelf === "Iluvatar" || userSelf === "Ajbura" || userSelf === "1997kB") // contrl-panel
                    document.getElementById("control-panel").style.display = "block";
                if (userRole === "none") {
                    document.getElementById("GSRRole").style.display = "block";
                    document.getElementById("GSRRole2").style.display = "block";
                }
                if (userRole !== "none") {
                    document.getElementById("GSRRole").style.display = "none";
                    document.getElementById("GSRRole2").style.display = "none";
                }

                if (settingslist['theme'] !== null && typeof settingslist['theme'] !== "undefined" && settingslist['theme'] !== "" && ( settingslist['theme'] >= 0 && settingslist['theme'] < (Object.keys(THEME)).length) )
                    themeIndex = parseInt(settingslist['theme']);

                if (settingslist['checkmode'] !== null && (typeof settingslist['checkmode'] !== "undefined") && settingslist['checkmode'] !== "") {
                    if (settingslist['checkmode'] === "1" || settingslist['checkmode'] === "2" || settingslist['checkmode'] === "0") {
                        checkMode = Number(settingslist['checkmode']);
                        document.getElementById("checkSelector").selectedIndex = checkMode;
                    }
                }

                if (settingslist['direction'] !== null && (typeof settingslist['direction'] !== "undefined") && settingslist['direction'] !== "") {
                    if (settingslist['direction'] === "1") {
                        document.getElementById("queue").setAttribute("style", "display:flex; flex-direction:column-reverse");
                        toggleTButton(document.getElementById('bottom-up-btn'));
                    }
                }

                if (settingslist['rhand'] !== null && (typeof settingslist['rhand'] !== "undefined") && settingslist['rhand'] !== "") {
                    if (settingslist['rhand'] === "1")
                        toggleTButton(document.getElementById("RH-mode-btn"));
                }

                languageIndex = "en";
                if (settingslist['lang'] !== null && settingslist['lang'] !== "" && (typeof settingslist['lang'] !== "undefined") && settingslist['lang'] !== "")
                    languageIndex = settingslist['lang'];

                function getLocale(locale) {
                    localeList = [];
                    if (locale)
                        localeList.push(locale);
                    let localeTmp = (navigator.userLanguage) ? navigator.userLanguage : navigator.language;
                    localeTmp = (typeof localeTmp === "object") ? localeTmp[0] : localeTmp;
                    localeList.push(Intl.getCanonicalLocales(localeTmp)[0]);
                    localeList.push("en-US");
                    return localeList;
                }

                var localeTmp = getLocale(false);
                if (settingslist['locale'] !== null && settingslist['locale'] !== "" && (typeof settingslist['locale'] !== "undefined") && settingslist['locale'] !== "") {
                    localeTmp = getLocale(settingslist['locale']);
                }
                var locale = localeTmp;

                if (settingslist['terminateStream'] !== null && (typeof settingslist['terminateStream'] !== "undefined") && settingslist['terminateStream'] !== "") {
                    if (settingslist['terminateStream'] === "1") {
                        toggleTButton(document.getElementById("terminate-stream-btn"));
                    }
                }

                if (settingslist['hotkeys'] !== null && (typeof settingslist['hotkeys'] !== "undefined") && settingslist['hotkeys'] !== "") {
                    if (settingslist['hotkeys'] === "1" || settingslist['hotkeys'] === 1) {
                        hotkeys = 1;
                        toggleTButton(document.getElementById("hotkeys-btn"));
                    }
                }

                if (settingslist['jumps'] !== null && (typeof settingslist['jumps'] !== "undefined") && settingslist['jumps'] !== "") {
                    if (settingslist['jumps'] === "1" || settingslist['jumps'] === 1) {
                        jumps = 1;
                        toggleTButton(document.getElementById("jumps-btn"));
                    }
                }

                if (settingslist['mobile'] !== null && (typeof settingslist['mobile'] !== "undefined") && settingslist['mobile'] !== "") {
                    if (settingslist['mobile'] === "1" || settingslist['mobile'] === "2" || settingslist['mobile'] === "3" || settingslist['mobile'] === "0")
                        resizeDrawer(Number(settingslist['mobile']), true);
                }

                if (settingslist['sound'] !== null && (typeof settingslist['sound'] !== "undefined") && settingslist['sound'] !== "") {
                    sound = Number(settingslist['sound']);
                    document.getElementById("soundSelector").selectedIndex = sound;
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

                function getPresets(setList, callback) {
                    $.ajax({url: 'php/presets.php?action=get_presets', type: 'POST', crossDomain: true, dataType: 'json',
                        success: function(presetsResp) {
                            presets = presetsResp;
                            let currentPresetNotNull = (setList["preset"] === null || setList["preset"] === "null") ? "Default" : setList["preset"];
                            presets.forEach(function(el, index) {
                                if (el["title"] === currentPresetNotNull)
                                    selectedPreset = index;
                                if (el["namespaces"] === null) presets[index]["namespaces"] = "";
                                if (el["blprojects"] === null) presets[index]["blprojects"] = "";
                                if (el["wikilangs"] === null) presets[index]["wikilangs"] = "";
                                if (el["wlprojects"] === null) presets[index]["wlprojects"] = "";
                                if (el["wlusers"] === null) presets[index]["wlusers"] = "";
                            });
                            document.getElementById('presetsArrow').classList.remove('disabled');
                            document.getElementById('editCurrentPreset').classList.remove('disabled');
                            callback();
                        }
                    });
                }

                /*----themes----*/
                function loadThemeList() {
                    for(name in Object.keys(THEME)) {
                        var option = document.createElement('option');
                        option.innerHTML = Object.keys(THEME)[name];
                        document.getElementById('themeSelector').appendChild(option);
                    }
                }
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

                    /*-----Send theme to iframes-------*/
                    let strTheme = getStrTheme(THEME);

                    var welcomeIF = document.getElementById("page-welcome").contentWindow;
                    welcomeIF.postMessage({ THEME, user: '<?php echo $userSelf; ?>' }, window.origin);

                    if (diffstart !== undefined && newstart !== undefined) {
                        diffstart = setStrTheme(diffstart, strTheme);
                        newstart = setStrTheme(newstart, strTheme);
                    }
                    if(document.getElementById("page").srcdoc !== "") {
                        document.getElementById("page").srcdoc = setStrTheme(document.getElementById("page").srcdoc, strTheme);
                    }
                }
                function changeTheme(select) {
                    if (select === undefined) select = 0;
                    setTheme(THEME[Object.keys(THEME)[select]]);
                    if (document.getElementById('cpLink') !==  null) document.getElementById('cpLink').href = "https://swviewer.toolforge.org/php/control.php?themeIndex=" + window.themeIndex;
                }

                function setSystemDefaultTheme() {
                    let systemTheme = window.getComputedStyle(document.documentElement).getPropertyValue('--system-theme');
                    if (systemTheme === 'dark') changeTheme(2);
                    else changeTheme(0);
                }

                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                    if (themeIndex !== 4) return;
                    setSystemDefaultTheme();
                });

                /*------Lang------*/

                function loadLocaleList() {
                    $.ajax({type: 'POST', url: 'lists/locales.txt', dataType: 'json',
                        success: function(locales) {
                            for(let locale_sel in locales) {
                                if (locales.hasOwnProperty(locale_sel)) {
                                    let option = document.createElement('option');
                                    option.innerHTML = locales[locale_sel];
                                    option.value = locale_sel;
                                    document.getElementById('localeSelector').appendChild(option);
                                }
                            }
                            setLocaleSelector(locale[0]);
                        }
                    });
                }

                function changeLocaleSelector() {
                    // changeLanguage(document.getElementById('localeSelector').value, false);
                    $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: { 'action': 'set', query: 'locales', locale: document.getElementById("localeSelector").value }, dataType: 'json'});
                    if (confirm(useLang["settings-confirm-lang"])) document.location.reload(true);
                }

                function loadLanguageList() {
                    $.ajax({url: 'php/localisation.php?init', type: 'POST', crossDomain: true, dataType: 'json',
                        success: function(language) {
                            for(name in language) {
                                if (language.hasOwnProperty(name)) {
                                    var option = document.createElement('option');
                                    option.innerHTML = language[name][0];
                                    option.value = name;
                                    document.getElementById('languageSelector').appendChild(option);
                                }
                            }

                            if (languageIndex) {
                                $.ajax({url: 'php/localisation.php?mycode=' + languageIndex, type: 'GET', crossDomain: true, dataType: 'json',
                                    success: function(language) {
                                        if (language["code"] === languageIndex) {
                                            setLanguageSelector(languageIndex);
                                            changeLanguage(languageIndex, true, language);
                                        } else {
                                            languageIndex = language["code"];
                                            setLanguageSelector("en");
                                            changeLanguage("en", true, language);
                                        }
                                    }
                                });
                            } else {
                                setLanguageSelector("en");
                                changeLanguage("en", true, language);
                            }
                        }
                    });
                }

                function changeLanguageSelector() {
                    changeLanguage(document.getElementById('languageSelector').value, false);
                    $.ajax({url: 'php/settings.php', type: 'POST', crossDomain: true, data: { 'action': 'set', query: 'lang', lang: document.getElementById("languageSelector").value }, dataType: 'json'});
                }

                async function changeLanguage(select, isLoad, language) {
                    var langAsync;
                    if (language)
                        langAsync = language;
                    else {
                        let responseLang = await fetch("php/localisation.php?init");
                        langAsync = await responseLang.json();
                    }

                    if (select === undefined || (!language && !langAsync.hasOwnProperty(select))) select = "en";
                    if (language) dirLang = langAsync["dir"]; else dirLang = langAsync[select][1];
                    $.ajax({url: 'i18n/en.json', crossDomain: true, dataType: 'json',
                        success: function(baseLang) {
                            if (select === "en") {
                                useLang = baseLang;
                                if (isLoad === false) {
                                    if (confirm(useLang["settings-confirm-lang"])) document.location.reload(true);
                                    return;
                                } else setLanguage(useLang, dirLang);
                            } else {
                                $.ajax({url: "i18n/" + select + ".json", crossDomain: true, dataType: 'json',
                                    success: function(selectLang) {
                                        for (m in baseLang) {
                                            if (baseLang.hasOwnProperty(m)) {
                                                if (m !== '@metadata') {
                                                    if (selectLang.hasOwnProperty(m)) {
                                                        if (selectLang[m] !== "" && selectLang[m] !== null) useLang[m] = selectLang[m];
                                                        else useLang[m] = baseLang[m]
                                                    } else
                                                        useLang[m] = baseLang[m];
                                                }
                                            }
                                        }
                                        useLang["@metadata"]["authors"] = selectLang["@metadata"]["authors"];
                                        useLang["utc"] = "(UTC)";
                                        changeTimeFormat(true);
                                        if (isLoad === false) {
                                            if (confirm(useLang["settings-confirm-lang"])) document.location.reload(true);
                                        } else setLanguage(useLang, dirLang);
                                    }
                                });
                            }

                            document.getElementById("soundSelector").selectedIndex = sound;
                            $.getScript('https://swviewer.toolforge.org/js/modules/talk.js', () => removeTabNotice('btn-talk'));
                            $.getScript('https://swviewer.toolforge.org/js/modules/logs.js', () => removeTabNotice('btn-logs'));
                            $.getScript('https://swviewer.toolforge.org/js/modules/about.js', () => removeTabNotice('btn-about'));
                            $.getScript('https://swviewer.toolforge.org/js/modules/notification.js', () => removeTabNotice('btn-notification'));

                        }
                    });
                }

                function setLanguage(messagesLanguage, dirLanguage) {
                    var elementsLang = [];
                    elementsLang[0] = document.getElementsByClassName("custom-lang");
                    elementsLang[1] = document.getElementById('editPresetTemplate').content.querySelectorAll('.custom-lang');
                    elementsLang[2] = document.getElementById('editPTitleTemplate').content.querySelectorAll('.custom-lang');
                    document.getElementById("parentHTML").setAttribute("dir", dirLanguage);
                    document.getElementById("parentHTML").setAttribute("lang", languageIndex);

                    for (els in elementsLang) {
                        for (el in elementsLang[els]) {
                            if (elementsLang[els].hasOwnProperty(el)) {
                                var attrs = elementsLang[els][el].attributes;
                                for (l in attrs) {
                                    if (attrs.hasOwnProperty(l)) {
                                        if (typeof attrs[l].value !== "undefined")
                                            if (messagesLanguage.hasOwnProperty(attrs[l].value.replace("[", "").replace("]", ""))) {
                                                //    elementsLang[els][el].setAttribute("dir", dirLanguage);
                                                elementsLang[els][el].setAttribute(attrs[l].name, messagesLanguage[attrs[l].value.replace("[", "").replace("]", "")]);
                                            }
                                        if (attrs[l].name === 'i-tooltip' && dirLanguage === "rtl") {
                                            if (attrs[l].value.match('left')) elementsLang[els][el].setAttribute(attrs[l].name, attrs[l].value.replace("left", "right"));
                                            else if (attrs[l].value.match('right')) elementsLang[els][el].setAttribute(attrs[l].name, attrs[l].value.replace("right", "left"));
                                        }
                                    }
                                }
                            }


                            if (elementsLang[els].hasOwnProperty(el)) {
                                if (typeof elementsLang[els][el].value !== "undefined")
                                    if (messagesLanguage.hasOwnProperty(elementsLang[els][el].value.replace("[", "").replace("]", ""))) {
                                        //     elementsLang[els][el].setAttribute("dir", dirLanguage);
                                        elementsLang[els][el].value = messagesLanguage[elementsLang[els][el].value.replace("[", "").replace("]", "")];
                                    }

                                if (typeof elementsLang[els][el].textContent !== "undefined")
                                    if (messagesLanguage.hasOwnProperty(elementsLang[els][el].textContent.replace("[", "").replace("]", ""))) {
                                        //    elementsLang[els][el].setAttribute("dir", dirLanguage);
                                        elementsLang[els][el].textContent = messagesLanguage[elementsLang[els][el].textContent.replace("[", "").replace("]", "")];
                                    }
                            }
                        }
                    }

                    if (isGlobal === true || isGlobalModeAccess === true) sandwichLocalisation(document, dirLang, useLang['presets-additional-desc'], document.getElementById('editPresetTemplate').content.getElementById("adw"), "$1", 4, "inline", "A", "https://meta.wikimedia.org/wiki/Special:MyLanguage/SWViewer/wikis", document.getElementById('editPresetTemplate').content);
                    sandwichLocalisation(document, dirLang, useLang['presets-ns-desc'], document.getElementById('editPresetTemplate').content.getElementById("ns-desc"), "$1", 4, "inline", "Ns", "https://en.wikipedia.org/wiki/Help:MediaWiki_namespace", document.getElementById('editPresetTemplate').content);
                    sandwichLocalisation(document, dirLang, useLang['settings-hotkeys-descr'], document.getElementById("hotkeys-descr"), "$1", 4, "inline", "Hk", "https://www.mediawiki.org/wiki/Manual:SWViewer#Hotkeys");

                    var welcomeIF = document.getElementById("page-welcome").contentWindow;
                    var useLangWelcome = generateMinMessages(useLang, /^welcome-frame-/); useLangWelcome["delete"] = useLang["delete"];
                    welcomeIF.postMessage({ lang: languageIndex, orient: dirLang, messages: useLangWelcome }, window.origin);
                    document.getElementById('loading').style.display = "none";
                    document.getElementById('app').style.display = "block";
                }

                function generateMinMessages(messagesList, pattern) {
                    var useLangMin = [];
                    for (messagename in messagesList) {
                        if (messagesList.hasOwnProperty(messagename))
                            if (pattern.test(messagename))
                                useLangMin[messagename] = messagesList[messagename];

                    }
                    return useLangMin;
                }

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
                }
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

                /*###################
                ------- Common -------
                #####################*/

                function isMobile() {
                    return window.getComputedStyle(document.getElementById('statusbar'), null).getPropertyValue('display') === 'none';
                }

                function scrollToBottom(id){
                    if (document.getElementById(id) !== null) {
                        document.getElementById(id).scrollTop = document.getElementById(id).scrollHeight;
                    }
                }

                function classToggler (el, cssClass) {
                    if (el.classList.contains(cssClass)) {
                        return el.classList.remove(cssClass);
                    }
                    el.classList.add(cssClass);
                }

                function setLanguageSelector(l) {
                    var options = document.getElementById('languageSelector').options;
                    for(var i = 0; i < options.length; i++) {
                        if(options[i].value === l) {
                            options[i].selected = true;
                            useLang["@metadata"]["langName"] = options[i].text;
                            break;
                        }
                    }
                }

                function setLocaleSelector(l) {
                    var options = document.getElementById('localeSelector').options;
                    for(var i = 0; i < options.length; i++) {
                        if(options[i].value === l) {
                            options[i].selected = true;
                            break;
                        }
                    }
                }

                function getUTCtime(timeLocale, typeTime) {
                    let tFormat = (typeTime === "short") ? "LT" : "LLLL";
                    return moment.utc().locale(timeLocale).format(tFormat);
                }
                function toggleTButton (button) { classToggler(button, 't-btn__active'); }
                function toggleICheckBox (checkbox) { classToggler(checkbox, 'i-checkbox__active'); }
                statusbarTimeFormat = "long";
                var statusbarTimeFormat = "long";
                function changeTimeFormat(isLoad) {
                    if (statusbarTimeFormat !== "long") {
                        if (isLoad === false)
                            statusbarTimeFormat = "long";
                        document.getElementById('statusbarTime').textContent = getUTCtime(locale, statusbarTimeFormat);
                        return;
                    }
                    if (isLoad === false)
                        statusbarTimeFormat = "short";
                    document.getElementById('statusbarTime').textContent = getUTCtime(locale, statusbarTimeFormat);
                }
                function searchMyLogs(actionIndex) {
                    if (typeof actionIndex === 'undefined') document.getElementById('actionSelector').selectedIndex = 0;
                    else document.getElementById('actionSelector').selectedIndex = actionIndex;
                    document.getElementById('actionSelector').onchange();
                    document.getElementById('logsSearch-input').value = userSelf;
                    document.getElementById('btn-searchLogs').click();
                    openPW('logs');
                }
            </script>
            <script src="js/swv.js?v=6"></script>
            <script>

                /*#########################
                --------- onLoad -------
                #########################*/

                window.onload = function() {
                    document.getElementById('loadingBar').style.width = '100%';
                    loadThemeList();
                    if (window.themeIndex) {
                        document.getElementById('themeSelector').selectedIndex = window.themeIndex;
                        if (window.themeIndex === 4) setSystemDefaultTheme();
                        else changeTheme(window.themeIndex);
                    } else changeTheme(0);
                    loadLanguageList();
                    loadLocaleList();

                    document.getElementById('statusbarTime').textContent = getUTCtime(locale, statusbarTimeFormat);
                    setInterval(() => {
                        document.getElementById('statusbarTime').textContent = getUTCtime(locale, statusbarTimeFormat);
                    }, 30000);

                    Guesture.onSwipe(document.getElementById('page-welcome').contentDocument.body, "rightSwipe", () => openSidebar());
                };
            </script>
</body>
</html>