<?php
require_once '/data/project/swviewer/vendor/autoload.php';
use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;
header("Expires: Thu, 01 Jan 1970 00:00:01 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');

session_cache_limiter('private');
session_cache_expire(0);

session_name( 'SWViewer' );
session_start();
$config = require_once '/data/project/swviewer/security/config.php';
$conf = new ClientConfig( $config['url'] );
$conf->setConsumer( new Consumer( $config['consumer_key'], $config['consumer_secret'] ) );
$client = new Client( $conf );
$accessToken = new Token( $_SESSION['tokenKey'], $_SESSION['tokenSecret'] );