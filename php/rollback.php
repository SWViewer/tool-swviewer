<?php
header('Content-Type: application/json; charset=utf-8');

session_name( 'SWViewer' );
session_start();
if ((isset($_SESSION['tokenKey']) == false) or (isset($_SESSION['tokenSecret']) == false) or (isset($_SESSION['userName']) == false) or (isset($_POST["page"]) == false) or (isset($_POST["wiki"]) == false) or (isset($_POST["project"]) == false) or (isset($_POST["user"]) == false)) {
    echo "Invalid request";
    session_write_close();
    exit(0);
}
$gTokenKey = $_SESSION['tokenKey'];
$gTokenSecret = $_SESSION['tokenSecret'];
$userName = $_SESSION['userName'];
$page = $_POST["page"];
$project = $_POST["project"];
$user = $_POST["user"];
$wiki = $_POST["wiki"];
session_write_close();

$errorCode = 200;
$inifile = '/data/project/swviewer/security/oauth-sw.ini';
$ini = parse_ini_file( $inifile );
if ( $ini === false ) {
	header( "HTTP/1.1 $inifile $ini Internal Server Error" );
	echo 'The ini file could not be read';
	exit(0);
}
if ( !isset( $ini['agent'] ) ||
	!isset( $ini['consumerKey'] ) ||
	!isset( $ini['consumerSecret'] )
) {
	header( "HTTP/1.1 202 Internal Server Error" );
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
	'type' => 'rollback',
    ), $ch );
        if ( !isset( $res->tokens->rollbacktoken ) ) {
		header( "HTTP/1.1 203 Internal Server Error" );
		echo 'Bad API response: <pre>' . htmlspecialchars( var_export( $res, 1 ) ) . '</pre>';
		exit(0);
	}
	$token = $res->tokens->rollbacktoken;



	// Now perform rollback
if (isset($_POST["summary"]) == false) {
$res = doApiQuery( array(
		'format' => 'json',
                'utf8' => '1',
		'action' => 'rollback',
		'title' => $page,
                'user' => $user,
		'token' => $token,
	), $ch );
} else {
$res = doApiQuery( array(
		'format' => 'json',
                'utf8' => '1',
		'action' => 'rollback',
		'title' => $page,
                'user' => $user,
                'summary' => $_POST["summary"],
		'token' => $token,
	), $ch );
}


if ( !isset( $res->rollback->title ) ) {
$res = json_decode(json_encode($res), True);
    $response = ["result" => $res["error"]["info"], "code" => $res["error"]["code"]];
    echo json_encode($response);
    exit(0);
}

$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);

$q = $db->prepare('INSERT INTO logs (user, type, wiki, title, diff) VALUES (:user, :type, :wiki, :title, :diff)');
$q->execute(array(':user' => $userName, ':type' => 'rollback', ':wiki' => $wiki, ':title' => strval($res->rollback->title), ':diff' => str_replace("/api.php", "/index.php?", $project) . 'oldid=' . strval($res->rollback->old_revid) . '&diff=' . strval($res->rollback->revid) . '/'));
$db = null;


$res = json_decode(json_encode($res), True);
$response = ["result" => "Success", "summary" => $res["rollback"]["summary"], "oldrevid" => $res["rollback"]["old_revid"], "newrevid" => $res["rollback"]["revid"], "user" => $userName];
echo json_encode($response);


exit(0);





// Based on https://tools.wmflabs.org/oauth-hello-world/index.php?action=download.
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