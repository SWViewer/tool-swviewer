<?php
# Script based on https://tools.wmflabs.org/oauth-hello-world/index.php?action=download.
session_name( 'SWViewer' );

if (isset($_GET["action"])) {
    # Unlogin
    if ($_GET["action"] == "unlogin") {
        session_start();
        $_SESSION = Array();
        session_write_close();
        echo "Unlogin is done";
        exit(0);
    }
    
    # Stage one (start of auth)
    $params = session_get_cookie_params();
    session_set_cookie_params($params['lifetime'], dirname($_SERVER['SCRIPT_NAME']));
    doPrepare();
    doAuthorizationRedirect();
}
else {
    # Stage two (end of auth)
    doPrepare();

    session_start();
    $gTokenKey = $_SESSION['tokenKey'];
    $gTokenSecret = $_SESSION['tokenSecret'];
    session_write_close();

    fetchAccessToken();

    $ch = null;
    $res = doApiQuery( array(
        'format' => 'json',
        'action' => 'query',
        'meta' => 'userinfo'
    ), $ch );
    session_start();
    $_SESSION['userName'] = $res->query->userinfo->name;

    $resGlobal = doApiQuery( array(
        'format' => 'json',
        'action' => 'query',
        'meta' => 'globaluserinfo',
        'guiuser' => $res->query->userinfo->name,
        'guiprop' => 'groups|merged',
        'utf8' => '1',
        'formatversion' => '2'
    ), $ch );

    $globalInfo = json_decode(json_encode($resGlobal), True);

    $global = false;
    forEach($globalInfo['query']['globaluserinfo']['groups'] as $globalGroup) {
        if ($globalGroup == 'steward' || $globalGroup == 'global-sysop' || $globalGroup == 'global-rollbacker')
            $global = true;
    }
    if ($global == true || $res->query->userinfo->name == "Ajbura" || $res->query->userinfo->name == "Exoped")
        $_SESSION['mode'] = 'global';
    else {
        $checkLocal = false;

        $patrollerGroup = ["dawiki", "enwikivoyage", "frwikisource", "frwiktionary", "hewiktionary", "hewiki", "hewikinews", "hewikibooks", "hrwiki", "itwikiversity", "itwikibooks", "itwikivoyage", "itwiktionary", "mkwiki", "nnwiki", "nowiki", "nowikibooks", "zhwikiversity", "zhwikivoyage", "metawiki", "bgwiki", "dawiki", "trwiki"];
        $editorGroup = ["plwiki", "zh_classicalwiki", "kawiki", "dewiki", "trwikiquote", "enwikibooks", "elwikinews", "enwikinews", "fawikinews", "huwiki", "plwikisource", "ptwikibooks"];
        $eliminatorGroup = ["fawiki", "viwiki", "viwikibooks"];
        $botAdminGroup = ["mlwiki", "ckbwiki", "frwiktionary"];
        $testSysopGroup = ["incubatorwiki"];
        $wikidataStaffGroup = ["wikidatawiki", "testwikidatawiki" ];
        $curatorGroup = ["enwikiversity"];
        $wrongsysop = ['aawiki', 'aawiktionary', 'aawikibooks', 'abwiktionary', 'akwiktionary', 'akwikibooks', 'amwikiquote', 'angwikibooks', 'angwikiquote', 'angwikisource', 'aswiktionary', 'aswikibooks', 'astwikibooks', 'astwikiquote', 'avwiktionary', 'aywikibooks', 'bhwiktionary', 'biwiktionary', 'biwikibooks', 'bmwiktionary', 'bmwikibooks', 'bmwikiquote', 'bowiktionary', 'bowikibooks', 'chwiktionary', 'chwikibooks', 'chowiki', 'cowikibooks', 'cowikiquote', 'crwiktionary', 'crwikiquote', 'dzwiktionary', 'gawikibooks', 'gawikiquote', 'gnwikibooks', 'gotwikibooks', 'guwikibooks', 'howiki', 'htwikisource', 'huwikinews', 'hzwiki', 'iewikibooks', 'iiwiki', 'ikwiktionary', 'kjwiki', 'kkwikiquote', 'knwikibooks', 'krwiki', 'krwikiquote', 'kswikibooks', 'kswikiquote', 'kwwikiquote', 'lbwikibooks', 'lbwikiquote', 'lnwikibooks', 'lvwikibooks', 'mhwiki', 'mhwiktionary', 'miwikibooks', 'mnwikibooks', 'muswiki', 'mywikibooks', 'nawikibooks', 'nawikiquote', 'nahwikibooks', 'ndswikibooks', 'ndswikiquote', 'ngwiki', 'piwiktionary', 'pswikibooks', 'quwikibooks', 'quwikiquote', 'rmwiktionary', 'rmwikibooks', 'rnwiktionary', 'scwiktionary', 'sdwikinews', 'sewikibooks', 'simplewikibooks', 'simplewikiquote', 'snwiktionary', 'suwikibooks', 'swwikibooks', 'thwikinews', 'tkwikibooks', 'tkwikiquote', 'towiktionary', 'ttwikiquote', 'twwiktionary', 'ugwikibooks', 'ugwikiquote', 'uzwikibooks', 'vowikibooks', 'vowikiquote', 'wawikibooks', 'xhwiktionary', 'xhwikibooks', 'yowiktionary', 'yowikibooks', 'zawiktionary', 'zawikibooks', 'zawikiquote', 'zh_min_nanwikibooks', 'zh_min_nanwikiquote', 'zuwikibooks', 'advisorywiki', 'nzwikimedia', 'pa_uswikimedia', 'qualitywiki', 'strategywiki', 'tenwiki', 'usabilitywiki', 'vewikimedia', 'wikimania2005wiki', 'wikimania2006wiki', 'wikimania2007wiki', 'wikimania2008wiki', 'wikimania2009wiki', 'wikimania2010wiki', 'wikimania2011wiki', 'wikimania2012wiki', 'wikimania2013wiki', 'wikimania2014wiki', 'wikimania2015wiki', 'wikimania2016wiki', 'wikimania2017wiki', 'wikimania2018wiki'];
        
        forEach($globalInfo['query']['globaluserinfo']['merged'] as $localGroups) {
            if (array_key_exists('groups', $localGroups))
                forEach($localGroups['groups'] as $localGroup) {
                    if ( ($localGroup == 'rollbacker' || ( $localGroup == 'sysop' && !in_array($localGroups['wiki'], $wrongsysop)) || ($localGroup == 'editor' && in_array($localGroups['wiki'], $editorGroup)) || ($localGroup == 'patroller' && in_array($localGroups['wiki'], $patrollerGroup)) || ($localGroup == 'eliminator' && in_array($localGroups['wiki'], $eliminatorGroup)) || ($localGroup == 'botadmin' && in_array($localGroups['wiki'], $botAdminGroup)) || ($localGroup == 'test-sysop' && in_array($localGroups['wiki'], $testSysopGroup)) || ($localGroup == 'wikidata-staff' && in_array($localGroups['wiki'], $wikidataStaffGroup)) || ($localGroup == 'curator' && in_array($localGroups['wiki'], $curatorGroup)) ) && ($localGroups['wiki'] !== 'testwiki' && $localGroups['wiki'] !== 'test2wiki' && $localGroups['wiki'] !== 'testwikidata' && $localGroups['wiki'] !== 'labtest' && $localGroups['wiki'] !== 'testcommons')) {
                        if (isset($_SESSION['projects']))
                            $_SESSION['projects'] .= $localGroups['wiki'] . ',';
                        else
                            $_SESSION['projects'] = $localGroups['wiki'] . ',';
                        $checkLocal = true;
                    }
                }
        }
        if ($checkLocal == true)
            $_SESSION['mode'] = 'local';
        else {
            $_SESSION = Array();
            session_write_close();
            header("Location: https://tools.wmflabs.org/swviewer?error=rights");
            exit(0);
        }
    }

    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
    $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
    unset($ts_mycnf, $ts_pw);

    $q = $db->prepare('SELECT name, token FROM user WHERE name=:name');
    $q->execute(array(':name' => $res->query->userinfo->name));
    $resToken = $q->fetchAll();
    if ( ($q->rowCount() <= 0) || ($q->rowCount() > 0 && ($resToken[0]["token"] == null || $resToken[0]["token"] == "" || !isset($resToken[0]["token"])) ) ) {
        $salt = parse_ini_file("/data/project/swviewer/security/bottoken.ini")["salt"];
        $_SESSION['talkToken'] = md5(uniqid($res->query->userinfo->name, true).rand().md5($salt));

        if ($q->rowCount() <= 0) {
            $q = $db->prepare('INSERT INTO user (name, token) VALUES (:name, :token)');
            $q->execute(array(':name' => $res->query->userinfo->name, ':token' => $_SESSION['talkToken']));
        }
        else {
            $q = $db->prepare('UPDATE user SET token=:token WHERE name=:name');
            $q->execute(array(':name' => $res->query->userinfo->name, ':token' => $_SESSION['talkToken']));
        }
    }
    else
        $_SESSION['talkToken']= $resToken[0]["token"];
    session_write_close();

    $q = $db->prepare('UPDATE user SET runscount=runscount+1 WHERE name=:name');
    $q->execute(array(':name' => $res->query->userinfo->name));
    $db = null;

    header("Location: https://tools.wmflabs.org/swviewer/");
    exit(0);
}


# -----------------------------------

# Get credentials and others
function doPrepare() {
    global $mwOAuthUrl, $mwOAuthAuthorizeUrl, $gUserAgent, $gConsumerKey, $gConsumerSecret, $errorCode;

    # Credentials
    $inifile = '/data/project/swviewer/security/oauth-sw.ini';
    # Auth end-points
    $mwOAuthUrl = 'https://meta.wikimedia.org/w/index.php?title=Special:OAuth';
    $mwOAuthAuthorizeUrl = 'https://meta.wikimedia.org/wiki/Special:OAuth/authorize';
    # Error code
    $errorCode = 200;

    # Read credentials file
    $ini = parse_ini_file( $inifile );
    if ( $ini === false || !isset( $ini['agent'] ) || !isset( $ini['consumerKey'] ) || !isset( $ini['consumerSecret'] )) {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://tools.wmflabs.org/swviewer/?error=internal&devcode=001");
        exit(0);
    }
    $gUserAgent = $ini['agent'];
    $gConsumerKey = $ini['consumerKey'];
    $gConsumerSecret = $ini['consumerSecret'];
}

# User redirect to widget
function doAuthorizationRedirect() {
    global $mwOAuthUrl, $mwOAuthAuthorizeUrl, $gUserAgent, $gConsumerKey, $gTokenSecret, $errorCode;

    # First, we need to fetch a request token.
    # The request is signed with an empty token secret and no token key.
    $gTokenSecret = '';
    $url = $mwOAuthUrl . '/initiate';
    $url .= strpos( $url, '?' ) ? '&' : '?';
    $url .= http_build_query( array(
        'format' => 'json',
        'oauth_callback' => 'oob',
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
    ) );

    $signature = sign_request( 'GET', $url );
    $url .= "&oauth_signature=" . urlencode( $signature );
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        header( "HTTP/1.1 $errorCode Internal Server Error" );
        echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
        exit(0);
    }
    curl_close( $ch );

    $token = json_decode( $data );
    if ( (is_object( $token ) && isset( $token->error )) || !is_object( $token ) || !isset( $token->key ) || !isset( $token->secret ) ) {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://tools.wmflabs.org/swviewer/?error=internal&devcode=003");
        exit(0);
    }

    # Saving request-token
    session_start();
    $_SESSION['tokenKey'] = $token->key;
    $_SESSION['tokenSecret'] = $token->secret;
    session_write_close();

    # User redurect to widget
    $url = $mwOAuthUrl . '/authorize';
    $url .= strpos( $url, '?' ) ? '&' : '?';
    $url .= http_build_query( array('oauth_token' => $token->key, 'oauth_consumer_key' => $gConsumerKey) );
    header( "Location: $url" );
    exit(0);
}

# Receive callback and get Acess Token
function fetchAccessToken() {
    global $mwOAuthUrl, $gUserAgent, $gConsumerKey, $gTokenKey, $gTokenSecret, $errorCode;

    $url = $mwOAuthUrl . '/token';
    $url .= strpos( $url, '?' ) ? '&' : '?';
    $url .= http_build_query( array(
        'format' => 'json',
        'oauth_verifier' => $_GET['oauth_verifier'],
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
    ) );
    $signature = sign_request( 'GET', $url );
    $url .= "&oauth_signature=" . urlencode( $signature );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://tools.wmflabs.org/swviewer/?error=internal&devcode=005");
        exit(0);
    }
    curl_close( $ch );
    $token = json_decode( $data );
    if (is_object( $token ) && isset( $token->error ) || !is_object( $token ) || !isset( $token->key ) || !isset( $token->secret)) {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://tools.wmflabs.org/swviewer/?error=internal&devcode=005");
        exit(0);
    }

    # Saving Acess Token
    session_start();
    $_SESSION['tokenKey'] = $gTokenKey = $token->key;
    $_SESSION['tokenSecret'] = $gTokenSecret = $token->secret;
    session_write_close();
}

function sign_request( $method, $url, $params = array() ) {
    global $gConsumerSecret, $gTokenSecret;

    $parts = parse_url( $url );
    $scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : 'http';
    $host = isset( $parts['host'] ) ? $parts['host'] : '';
    $port = isset( $parts['port'] ) ? $parts['port'] : ( $scheme == 'https' ? '443' : '80' );
    $path = isset( $parts['path'] ) ? $parts['path'] : '';
    if ( ( $scheme == 'https' && $port != '443' ) ||
        ( $scheme == 'http' && $port != '80' )
    ) {
        $host = "$host:$port";
    }
    $pairs = array();
    parse_str( isset( $parts['query'] ) ? $parts['query'] : '', $query );
    $query += $params;
    unset( $query['oauth_signature'] );
    if ( $query ) {
        $query = array_combine(
            array_map( 'rawurlencode', array_keys( $query ) ),
            array_map( 'rawurlencode', array_values( $query ) )
        );
        ksort( $query, SORT_STRING );
        foreach ( $query as $k => $v ) {
            $pairs[] = "$k=$v";
        }
    }

    $toSign = rawurlencode( strtoupper( $method ) ) . '&' .
        rawurlencode( "$scheme://$host$path" ) . '&' .
        rawurlencode( join( '&', $pairs ) );
    $key = rawurlencode( $gConsumerSecret ) . '&' . rawurlencode( $gTokenSecret );
    return base64_encode( hash_hmac( 'sha1', $toSign, $key, true ) );
}

function doApiQuery( $post, &$ch = null ) {
    global $gUserAgent, $gConsumerKey, $gTokenKey, $errorCode;

    $headerArr = array(
        'oauth_consumer_key' => $gConsumerKey,
        'oauth_token' => $gTokenKey,
        'oauth_version' => '1.0',
        'oauth_nonce' => md5( microtime() . mt_rand() ),
        'oauth_timestamp' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
    );
    $signature = sign_request( 'POST', 'https://meta.wikimedia.org/w/api.php', $post + $headerArr );
    $headerArr['oauth_signature'] = $signature;

    $header = array();
    foreach ( $headerArr as $k => $v ) {
        $header[] = rawurlencode( $k ) . '="' . rawurlencode( $v ) . '"';
    }
    $header = 'Authorization: OAuth ' . join( ', ', $header );

    if ( !$ch ) {
        $ch = curl_init();
    }
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_URL, 'https://meta.wikimedia.org/w/api.php' );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $header ) );
    curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    $data = curl_exec( $ch );
    if ( !$data ) {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://tools.wmflabs.org/swviewer/?error=internal&devcode=008");
        exit(0);
    }
    $ret = json_decode( $data );
    if ( $ret === null ) {
        $_SESSION = Array();
        session_write_close();
        header("Location: https://tools.wmflabs.org/swviewer/?error=internal&devcode=009");
        exit(0);
    }
    return $ret;
}
?>