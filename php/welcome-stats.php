<?php
    header('Content-Type: application/json');
    session_name( 'SWViewer' );
    session_start();
    if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) ) {
        echo "Invalid request";
        session_write_close();
        exit();
    }
    $user = $_SESSION['userName'];
    session_write_close();
    
    $_POST = json_decode(file_get_contents('php://input'),true);
    $searchPhrase = $_POST['user'];

    $ts_pw = posix_getpwuid(posix_getuid());
    $ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
    $db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
    unset($ts_mycnf, $ts_pw);

    // $q = $db->query('SELECT count(*) AS total FROM user');
    // $result = $q->fetchAll();
    // $total_users = $result[0]["total"];

    if ($searchPhrase !== '') {
        $q = $db->prepare('SELECT count(*) AS total, type FROM logs WHERE user = :userName GROUP BY type');
        $q->execute(array(':userName' => $searchPhrase));
        $user_stats = $q->fetchAll();

        echo json_encode($user_stats);
    } else {
        $q = $db->query('SELECT count(*) AS total, type FROM logs GROUP BY type');
        $users_stats = $q->fetchAll();
        echo json_encode($users_stats);
    }
?>