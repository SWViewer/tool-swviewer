<?php
header('Content-Type: application/json; charset=utf-8');
session_name( 'SWViewer' );
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName']) || !isset($_POST["page"]) || !isset($_POST["wiki"]) || !isset($_POST["project"]) || !isset($_POST["user"]) || !isset($_POST["rbmode"])) {
    echo "Invalid request";
    session_write_close();
    exit(0);
}
$basetimestamp = "";
$id = "";
if ($_POST["rbmode"] === "undo") {
    if (isset($_POST["basetimestamp"]) && isset($_POST["id"])) {
        $basetimestamp = $_POST["basetimestamp"];
        $id = $_POST["id"];
    }
    else {
        echo "Invalid request (undo mode)";
        session_write_close();
        exit(0);
    }
}
$gTokenKey = $_SESSION['tokenKey'];
$gTokenSecret = $_SESSION['tokenSecret'];
$userName = $_SESSION['userName'];
$page = $_POST["page"];
$project = $_POST["project"];
$user = $_POST["user"];
$wiki = $_POST["wiki"];
$mode = $_POST["rbmode"];
session_write_close();
$inifile = '/data/project/swviewer/security/oauth-sw.ini';
$ini = parse_ini_file( $inifile );
if ( $ini === false ) {
	header( "HTTP/1.1 $inifile $ini Internal Server Error" );
	echo 'The ini file could not be read';
	exit(0);
}
if (!isset( $ini['agent'] ) || !isset( $ini['consumerKey'] ) || !isset( $ini['consumerSecret'] )) {
	echo 'Required configuration directives not found in ini file';
	exit(0);
}
$gUserAgent = $ini['agent'];
$gConsumerKey = $ini['consumerKey'];
$gConsumerSecret = $ini['consumerSecret'];
$ch = null;
$token = "";
$rev = "";
$params = ['format' => 'json', 'action' => 'tokens', 'type' => 'rollback'];
$typetoken = "rollbacktoken";
if ($mode === "undo") {
    $params["type"] = "edit";
    $typetoken = "edittoken";
}
$res = doApiQuery( $params, $ch );
if (!isset($res->tokens->$typetoken)) {
    header( "HTTP/1.1 203 Internal Server Error" );
    echo 'Bad API response: <pre>' . htmlspecialchars( var_export( $res, 1 ) ) . '</pre>';
    exit(0);
}
$token = $res->tokens->$typetoken;
if ($token === "" || ($mode !== "rollback" && $mode !== "undo")) {
    header( "HTTP/1.1 203 Internal Server Error" );
    echo 'Bad API response.</pre>';
    exit();
}


// Now perform rollback or undo
if ($mode === "rollback") {
    $params = ['format' => 'json', 'utf8' => '1', 'action' => 'rollback', 'title' => $page, 'user' => $user, 'token' => $token];
    if (isset($_POST["summary"]))
        $params["summary"] = $_POST["summary"];
    $res = doApiQuery($params, $ch);
}
else {
    $res = doApiQuery( array('format' => 'json', 'utf8' => '1',
        'action' => 'query',
        'prop' => 'revisions',
        'titles' => $page,
        'rvprop' => 'ids|user',
        'rvlimit' => 1,
        'rvexcludeuser' => $user
    ), $ch );
    forEach($res->query->pages as $key=>$p) {
        if ($key !== "-1")
            $res2 = $p;
    }
    if ($res2 !== null)
        if ($res2->revisions[0]->revid !== "0")
            $rev = $res2->revisions[0]->revid;
    if ($rev !== "") {
        $summary = str_replace("$1", $res2->revisions[0]->user, "Restore to the last revision by [[User:$1|$1]]");;
        if (isset($_POST["summary"]))
            if ($_POST["summary"] !== "")
                $summary = str_replace("$1", $res2->revisions[0]->user, $_POST["summary"]);
        $res = doApiQuery( array('format' => 'json', 'utf8' => '1',
            'action' => 'edit',
            'title' => $page,
            'undo' => $id,
            'undoafter' => $rev,
            'nocreate' => '1',
            'watchlist' => 'nochange',
            'minor' => 1,
            'summary' => $summary,
            'basetimestamp' => $basetimestamp,
            'token' => $token
        ), $ch );
    }
}


// Cacthing bad responses
$typeaction = "rollback";
if ($mode === "undo")
    $typeaction = "edit";
if ( !isset($res->$typeaction->title) || isset($res->$typeaction->nochange)) {
    $res = json_decode(json_encode($res), True);
    if (isset($res[$typeaction]["nochange"]))
        $response = ["code" => "alreadyrolled", "result" => "Edits is already undid."];
    else
        $response = ["result" => $res["error"]["info"], "code" => $res["error"]["code"]];
    echo json_encode($response);
    exit(0);
}

// Send result to DB
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
if ($mode === "rollback")
    $q->execute(array(':user' => $userName, ':type' => 'rollback', ':wiki' => $wiki, ':title' => strval($res->rollback->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->rollback->old_revid) . '&diff=' . strval($res->rollback->revid) . '/'));
else
    $q->execute(array(':user' => $userName, ':type' => 'undo', ':wiki' => $wiki, ':title' => strval($res->edit->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->edit->oldrevid) . '&diff=' . strval($res->edit->newrevid) . '/'));
$db = null;

// Return result
$res = json_decode(json_encode($res), True);
if ($mode === "rollback")
    $response = ["result" => "Success", "summary" => $res["rollback"]["summary"], "oldrevid" => $res["rollback"]["old_revid"], "newrevid" => $res["rollback"]["revid"], "user" => $userName, "type" => "rolback"];
else
    $response = ["result" => "Success", "summary" => $summary, "oldrevid" => $res["edit"]["oldrevid"], "newrevid" => $res["edit"]["newrevid"], "user" => $userName, "type" => "undo"];
echo json_encode($response);


// Based on https://tools.wmflabs.org/oauth-hello-world/index.php?action=download.
function sign_request( $method, $url, $params = array() ) {
	global $gConsumerSecret, $gTokenSecret;

	$parts = parse_url( $url );
	$scheme = isset( $parts['scheme'] ) ? $parts['scheme'] : 'http';
	$host = isset( $parts['host'] ) ? $parts['host'] : '';
	$port = isset( $parts['port'] ) ? $parts['port'] : ( $scheme == 'https' ? '443' : '80' );
	$path = isset( $parts['path'] ) ? $parts['path'] : '';
	if ( ( $scheme == 'https' && $port != '443' ) || ( $scheme == 'http' && $port != '80' ) ) {
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
	global $gUserAgent, $gConsumerKey, $gTokenKey, $errorCode, $page, $project;

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
		header( "HTTP/1.1 204 Internal Server Error" );
		echo 'Curl error: ' . htmlspecialchars( curl_error( $ch ) );
		exit(0);
	}
	$ret = json_decode( $data );
	if ( $ret === null ) {
		header( "HTTP/1.1 205 Internal Server Error" );
		echo 'Unparsable API response: <pre>' . htmlspecialchars( $data ) . '</pre>';
		exit(0);
	}
	return $ret;
}
?>