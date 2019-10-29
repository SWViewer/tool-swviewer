<?php
    header('Content-Type: application/json');
    session_name( 'SWViewer' );
    session_start();
    if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) ) {
        echo "Invalid request";
        session_write_close();
        exit();
    }
    session_write_close();

    $_POST = json_decode(file_get_contents('php://input'),true);
    $searchPhrase = $_POST['sp'].'%';
    $searchType = $_POST['st'].'%';
    $limit = $_POST['li'];
    $offset = $_POST['of'];
    if ( (!is_numeric($limit) && !is_null($limit) && !isset($limit)) )
        exit();

    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
    $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
    unset($ts_mycnf, $ts_pw);

    $db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
    $q = $db->prepare('SELECT * FROM logs WHERE (user LIKE :searchUser OR wiki LIKE :searchWiki) AND (type LIKE :searchType) ORDER BY date DESC LIMIT :limit OFFSET :offset');
    $q->bindParam(':searchUser', $searchPhrase, PDO::PARAM_STR);
    $q->bindParam(':searchWiki', $searchPhrase, PDO::PARAM_STR);
    $q->bindParam(':searchType', $searchType, PDO::PARAM_STR);
    $q->bindParam(':limit', $limit, PDO::PARAM_INT);
    $q->bindParam(':offset', $offset, PDO::PARAM_INT);
    $q->execute();
    $logs = $q->fetchAll();
    echo json_encode($logs);
?>