<?php
    header('Content-Type: text/html;; charset=utf-8');
    session_name( 'SWViewer' );
    session_start();
    if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) ) {
        echo "Invalid request";
        session_write_close();
        exit();
    }
    session_write_close();
?>
   <div id="logsTable" class="logs-table">
    <div class="lt-row">
        <div class="lt__sno">SNo</div>
        <div class="lt__user">User</div>
        <div class="lt__action">Action</div>
        <div class="lt__wiki">Wiki</div>
        <div class="lt__title">Title</div>
        <div class="lt__date">Date</div>
    </div>
    <?php
        function getActionColor($action) {
            if($action == 'delete') return "#672dd2;";
            else if($action == 'rollback') return "var(--tc-lowSecondary)";
            else if($action == 'warn') return "#d92c26;";
            else if($action == 'report') return "#e3791c";
            else if($action == 'edit') return "#2dd280;";
            else return 'var(--tc-lowSecondary)';
        };

        $searchPhrase = $_GET['sp'].'%';
        $searchType = $_GET['st'].'%';
        $limit = $_GET['li'];
        $offset = $_GET['of'];
        $sno = $offset;
        if ( (!is_numeric($sno) && !is_null($sno) && !isset($sno)) || (!is_numeric($limit) && !is_null($limit) && !isset($limit)) )
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

        while($row = $q->fetch()) {
            $url = urldecode($row['diff']);
            $url = str_replace('%2F', '/', $url);
            $url = str_replace('%3A', ':', $url);
            $url = str_replace('%3F', '?', $url);
            $url = str_replace('%3D', '&', $url);
            
            $sno = $sno + 1;
            echo "<div class='lt-row'>";
                echo "<div class='lt__sno'>".htmlspecialchars($sno, ENT_QUOTES, 'UTF-8')."</div>";
                echo "<div class='lt__user'><a href='".substr($url, 0, (strpos($url, '.org/') + 5))."wiki/User:".htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8')."' target='_blank'>".htmlspecialchars($row['user'], ENT_QUOTES, 'UTF-8')."</a></div>";
                echo "<div class='lt__action' style='color: ".getActionColor(htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8'))."'>".htmlspecialchars($row['type'], ENT_QUOTES, 'UTF-8')."</div>";
                echo "<div class='lt__wiki'>".htmlspecialchars($row['wiki'], ENT_QUOTES, 'UTF-8')."</div>";
                echo "<div class='lt__title'><a href='".$url."' target='_blank'>".htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8')."</a></div>";
                echo "<div class='lt__date'>".htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8')."</div>";
            echo"</div>";
        }
        $db = null;
    ?>
</div>