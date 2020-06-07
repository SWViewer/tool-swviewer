<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');
session_name('SWViewer');
session_start();
if (!isset($_SESSION['tokenKey']) || !isset($_SESSION['tokenSecret']) || !isset($_SESSION['userName'])) {
    echo json_encode(["result" => "error", "info" => "Invalid request"]);
    session_write_close();
    exit();
}
session_write_close();

$url = "https://www.wikidata.org/w/api.php?action=wbgetentities&ids=" . urlencode($_GET['id']) . "&props=labels&languages=en&format=json&utf8=1";

try {
    $result = file_get_contents($url);
    $result = json_decode($result);

    if (isset($result->entities))
        if (isset($result->entities->$_GET['id']))
            if (isset($result->entities->$_GET['id']->labels))
                if (isset($result->entities->$_GET['id']->labels->en))
                    if (isset($result->entities->$_GET['id']->labels->en->value))
                        if ($result->entities->$_GET['id']->labels->en->value !== null && $result->entities->$_GET['id']->labels->en->value !== "") {
                            echo json_encode(["result" => "success", "label" => $result->entities->$_GET['id']->labels->en->value]);
                            exit();
                        }
    echo json_encode(["result" => "error"]);
} catch (Exception $e) {
    echo json_encode(["result" => "error"]);
}
?>