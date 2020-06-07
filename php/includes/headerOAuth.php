<?php
require_once '/data/project/swviewer/vendor/autoload.php';
use MediaWiki\OAuthClient\Client;
use MediaWiki\OAuthClient\ClientConfig;
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;
header('Content-Type: application/json; charset=utf-8');
session_name( 'SWViewer' );
session_start();
$config = require_once '/data/project/swviewer/security/config.php';
$conf = new ClientConfig( $config['url'] );
$conf->setConsumer( new Consumer( $config['consumer_key'], $config['consumer_secret'] ) );
$client = new Client( $conf );
$accessToken = new Token( $_SESSION['tokenKey'], $_SESSION['tokenSecret'] );