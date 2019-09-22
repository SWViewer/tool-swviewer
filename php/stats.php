<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>SWViewer</title>

        <link rel="apple-touch-icon" sizes="180x180" href="../img/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../img/favicons/favicon-16x16.png">
        <link rel="manifest" href="../site.webmanifest">
        <link rel="mask-icon" href="../img/favicons/safari-pinned-tab.svg" color="#5bbad5">
        <meta name='viewport' content='width=device-width, initial-scale=1'>
        <meta name="apple-mobile-web-app-title" content="SWViewer">
        <meta name="application-name" content="SWViewer">
        <meta name="author" content="Iluvatar">
        <meta name="description" content="App for viewing queue of edits on small wikis for SWMT">
        <meta name="keywords" content="SWMT, stats">

        <style>
            td {padding:5px}
            table {margin-bottom:10px}
            body {padding:8px}
        </style>
    </head>
<body>
<center><h2>Users of SWViewer</h2>
<table border="2">
<tr>
    <th>Username</th>
    <th>Runs</th>
</tr>
<?php
$ts_pw = posix_getpwuid(posix_getuid());
$ts_mycnf = parse_ini_file("/data/project/swviewer/security/replica.my.cnf");
$db = new PDO("mysql:host=tools.labsdb;dbname=s53950__SWViewer;charset=utf8", $ts_mycnf['user'], $ts_mycnf['password']);
unset($ts_mycnf, $ts_pw);
$q = $db->query('SELECT name, runscount FROM user  ORDER BY runscount DESC');
while($row = $q -> fetch()) {
    echo "<tr><td>".$row['name']. "</td><td>".$row['runscount']."</td></tr>";
}
$db = null;
?>
</table>
<a href='https://tools.wmflabs.org/swviewer/'>Main page</a></center>
</body>
</html>