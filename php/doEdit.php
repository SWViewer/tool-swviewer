<?php
header('Content-Type: application/json; charset=utf-8');

session_name( 'SWViewer' );
session_start();
if ((isset($_SESSION['tokenKey']) == false) or (isset($_SESSION['tokenSecret']) == false) or (isset($_SESSION['userName']) == false) or (isset($_POST["page"]) == false) or (isset($_POST["wiki"]) == false) or (isset($_POST["project"]) == false) or (isset($_POST["text"]) == false) or (isset($_POST["summary"]) == false) or (!isset($_POST["warn"]) && !isset($_POST["basetimestamp"])) ) {
    echo "Invalid request";
    session_write_close();
    exit(0);
}
$gTokenKey = $_SESSION['tokenKey'];
$gTokenSecret = $_SESSION['tokenSecret'];
$userName = $_SESSION['userName'];
session_write_close();

$page = $_POST["page"];
$project = $_POST["project"];
$text = $_POST["text"];
$summary = $_POST["summary"];
$timestamp = $_POST["basetimestamp"];
$wiki = $_POST["wiki"];

if (isset($_POST["checkreport"])) {
    if (isset($_POST["regexreport"]) && isset($_POST["user"])) {
        $project = str_replace("/api.php", "/index.php", $project);
        $res_content = @file_get_contents($project . "?action=raw&title=".urlencode($page));
        $project = str_replace("/index.php", "/api.php", $project);

        $regex = str_replace("$1", preg_quote($_POST["user"]), $_POST["regexreport"]);

        if (preg_match("/".$regex."/", $res_content)) 
            $response = ["result" => true];
        else
            $response = ["result" => false];
        echo json_encode($response);
    }
    exit();
}

$errorCode = 200;
$inifile = '/data/project/swviewer/security/oauth-sw.ini';
$ini = parse_ini_file( $inifile );
if ( $ini === false ) {
	header( "HTTP/1.1 $errorCode Internal Server Error" );
	echo 'The ini file could not be read';
	exit(0);
}
if ( !isset( $ini['agent'] ) ||
	!isset( $ini['consumerKey'] ) ||
	!isset( $ini['consumerSecret'] )
) {
	header( "HTTP/1.1 $errorCode Internal Server Error" );
	echo 'Required configuration directives not found in ini file';
	exit(0);
}
$gUserAgent = $ini['agent'];
$gConsumerKey = $ini['consumerKey'];
$gConsumerSecret = $ini['consumerSecret'];
$ch = null;

$res = doApiQuery( array(
	'format' => 'json',
	'action' => 'tokens',
	'type' => 'edit',
    ), $ch );
        if ( !isset( $res->tokens->edittoken ) ) {
		header( "HTTP/1.1 $errorCode Internal Server Error" );
		echo 'Bad API response: <pre>' . htmlspecialchars( var_export( $res, 1 ) ) . '</pre>';
		exit(0);
	}
	$token = $res->tokens->edittoken;



if (isset($_POST["warn"])) {
    $sectiontitle = $_POST["sectiontitle"];
    if ($_POST["warn"] == "rollback") {
        if ($_POST["withoutsection"] == "true") {
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'query', 'prop' => 'revisions', 'rvprop' => 'size', 'titles' => $page), $ch );
            $res2 = null;
            forEach($res->query->pages as $key=>$p) {
                if ($key !== "-1")
                    $res2 = $p;
            }
            if ($res2 !== null)
                if ($res2->revisions[0]->size !== "0")
                    $text = "\n\n"  .$text;
            $res = doApiQuery( array(
               'format' => 'json', 'utf8' => '1',
               'action' => 'edit',
               'title' => $page,
               'appendtext' => $text,
               'recreate' => '1',
               'watchlist' => 'nochange',
               'summary' => $summary,
               'token' => $token
            ), $ch );
        }
        else {
            $res = doApiQuery( array(
		'format' => 'json',
                'utf8' => '1',
		'action' => 'parse',
		'page' => $page,
                'prop' => 'sections'
	    ), $ch );
            $sectionNumber = "new";
            if (isset($res->parse))
                if (isset($res->parse->sections))
                    forEach($res->parse->sections as $section) {
                        if (isset($section->line))
                            if (isset($section->index))
                                if ($section->line == $sectiontitle)
                                    $sectionNumber = $section->index;
                    }
            if ($sectionNumber !== "new") {
                $project = str_replace("/api.php", "/index.php", $project);
                $res_content = @file_get_contents($project . "?action=raw&title=".urlencode($page)."&section=".$sectionNumber);
                $project = str_replace("/index.php", "/api.php", $project);
                $res = doApiQuery( array(
		    'format' => 'json',
                    'utf8' => '1',
		    'action' => 'edit',
		    'title' => $page,
                    'text' => $res_content . "\n\n" . $text,
                    'section' => $sectionNumber,
                    'recreate' => '1',
                    'watchlist' => 'nochange',
                    'summary' => $summary,
		    'token' => $token
	        ), $ch );
            }
            else
                $res = doApiQuery( array(
		    'format' => 'json',
                    'utf8' => '1',
		    'action' => 'edit',
		    'title' => $page,
                    'text' => $text,
                    'section' => 'new',
                    'sectiontitle' => $sectiontitle,
                    'recreate' => '1',
                    'watchlist' => 'nochange',
                    'summary' => $summary,
		    'token' => $token
	        ), $ch );
        }
        if (isset($res->edit->title)) {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'warn', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }
    if ($_POST["warn"] == "speedy") {
        if (isset($sectiontitle) && $sectiontitle !== "" && $sectiontitle !== null)
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        else {
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'query', 'prop' => 'revisions', 'rvprop' => 'size', 'titles' => $page), $ch );
            $res2 = null;
            forEach($res->query->pages as $key=>$p) {
                if ($key !== "-1")
                    $res2 = $p;
            }
            if ($res2 !== null)
                if ($res2->revisions[0]->size !== "0")
                    $text = "\n\n" . $text;
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'appendtext' => $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        }
        if (isset($res->edit->title)) {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'warn', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }

    if ($_POST["warn"] == "report") {
        if ($_POST["withoutsection"] !== "true")
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        else
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        
        if (isset($res->edit->title)) {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'report', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }

    if ($_POST["warn"] == "protect") {
        if ($_POST["withoutsection"] !== "true")
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        else
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        
        if (isset($res->edit->title)) {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'protect', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }

    if ($_POST["warn"] == "SRG") {
        if ($_POST["withoutsection"] !== "true")
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        else
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        
        if (isset($res->edit->title)) {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'SRG', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }

    if ($_POST["warn"] == "SRM") {
        if ($_POST["withoutsection"] !== "true")
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'text' => $text, 'section' => 'new', 'sectiontitle' => $sectiontitle, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        else
            $res = doApiQuery( array('format' => 'json', 'utf8' => '1', 'action' => 'edit', 'title' => $page, 'appendtext' => "\n\n" . $text, 'recreate' => '1', 'watchlist' => 'nochange', 'summary' => $summary, 'token' => $token), $ch );
        
        if (isset($res->edit->title)) {
            $ts_pw = posix_getpwuid(posix_getuid());
            $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
            $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
            unset($ts_mycnf, $ts_pw);

            $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
            $q->execute(array(':user' => $userName, ':type' => 'SRM', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
            $db = null;

            $response = ["result" => "sucess"];
            echo json_encode($response);
        }
    }

}
else {
    $res = doApiQuery( array(
		'format' => 'json',
                'utf8' => '1',
		'action' => 'edit',
		'title' => $page,
                'text' => $text,
                'summary' => $summary,
                'basetimestamp' => $timestamp,
                'nocreate' => '1',
		'token' => $token
    ), $ch );
}

if ( !isset( $res->edit->newrevid ) ) {
    $res = json_decode(json_encode($res), True);
    $debugFile = fopen("debug.txt", "a");
    $debugContent = print_r($res, true) . "\n";
    fwrite($debugFile, $debugContent);
    fclose($debugFile);

    if (isset($res->edit->info))
        $response = ["result" => $res["edit"]["info"], "code" => $res["edit"]["code"]];
    else {
        if (isset($res->error->info))
            $response = ["result" => $res["error"]["info"], "code" => $res["error"]["code"]];
        else
            $response = ["result" => "Unknow error", "code" => var_dump($res)];
    }
    echo json_encode($response);
    exit(0);
}



if (isset($res->edit->title) && !isset($_POST["warn"])) {
    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
    $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
    unset($ts_mycnf, $ts_pw);

    $actiontype = "edit";
    if (isset($_POST["isdelete"]))
        if ($_POST["isdelete"] == "true")
            $actiontype = "delete";
    $q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
    $q->execute(array(':user' => $userName, ':type' => $actiontype, ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
    $db = null;

    $res = json_decode(json_encode($res), True);
    $response = ["result" => "Success", "summary" => $summary, "oldrevid" => $res["edit"]["oldrevid"], "newrevid" => $res["edit"]["newrevid"], "user" => $userName];
    echo json_encode($response);
}

exit(0);





# Based on https://tools.wmflabs.org/oauth-hello-world/index.php?action=download.
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
	global $gUserAgent, $gConsumerKey, $gTokenKey, $errorCode, $project;

	$headerArr = array(
		'oauth_consumer_key' => $gConsumerKey,
		'oauth_token' => $gTokenKey,
		'oauth_version' => '1.0',
		'oauth_nonce' => md5( microtime() . mt_rand() ),
		'oauth_timestamp' => time(),
		'oauth_signature_method' => 'HMAC-SHA1',
	);
	$signature = sign_request( 'POST', $project, $post + $headerArr );
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
	curl_setopt( $ch, CURLOPT_URL, $project );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $post ) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( $header ) );
	//curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch, CURLOPT_USERAGENT, $gUserAgent );
	curl_setopt( $ch, CURLOPT_HEADER, 0 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	$data = curl_exec( $ch );
	if ( !$data ) {
		header( "HTTP/1.1 $errorCode Internal Server Error" );
		echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
		exit(0);
	}
	$ret = json_decode( $data );
	if ( $ret === null ) {
		header( "HTTP/1.1 $errorCode Internal Server Error" );
		echo 'Unparsable API response: <pre>' . htmlspecialchars( $data ) . '</pre>';
		exit(0);
	}
	return $ret;
}
?>