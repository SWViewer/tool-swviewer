<?php
header("Cache-Control: no-cache, no-stire, must-revalidate, max-age=0");
header('Content-Type: application/json; charset=utf-8');

$wikis = [];
$result = [];
$options = array('https' => array('method' => "POST", "User-Agent: SWViewer/1.3 (checkGS; swviewer@tools.wmflabs.org) PHP"));
$context = stream_context_create($options);

try {
    $url = "https://meta.wikimedia.org/w/api.php?action=query&format=json&list=wikisets&utf8=1&wsfrom=Opted-out%20of%20global%20sysop%20wikis&wsprop=wikisincluded";
    $content = getContent($url, $context, 10);
    $content = json_decode($content, true);
    if (isset($content["query"]["wikisets"][0]))
        foreach ($content["query"]["wikisets"][0]["wikisincluded"] as $wiki) {
            if ($wiki == "apiportalwiki")
                array_push($wikis, [$wiki, "api.wikimedia.org"]);
            else
                array_push($wikis, [$wiki, replace_wiki_name($wiki)]);
        }

    foreach ($wikis as $wiki) {
        $url = "https://" . $wiki[1] . "/w/api.php?action=query&format=json&list=allusers&utf8=1&augroup=sysop&aulimit=10";
        $content = getContent($url, $context, 10);
        if ($content == false)
            continue;
        $content = json_decode($content, true);
        $total_sysop = count($content["query"]["allusers"]);
        if ($total_sysop == 10)
            $result[$wiki[0]] = [$wiki[1], 2, strval($total_sysop), "?"];
        else {
            $actives = 0;
            foreach ($content["query"]["allusers"] as $sysop) {
                $sysop_url = urlencode($sysop["name"]);
                $url = "https://" . $wiki[1] . "/w/api.php?action=query&format=json&list=usercontribs&ucuser=" . $sysop_url . "&utf8=1&ucprop=timestamp&uclimit=1";
                $content = getContent($url, $context, 10);
                if ($content == false) {
                    $actives = 100;
                    break;
                }
                $content = json_decode($content, true);
                if (isset($content["query"]["usercontribs"][0]))
                    if (strtotime($content["query"]["usercontribs"][0]["timestamp"]) > strtotime('-2 months'))
                        $actives += 1;
                    else {
                        $url = "https://" . $wiki[1] . "/w/api.php?action=query&format=json&list=logevents&leuser=" . $sysop_url . "&utf8=1&leprop=timestamp&lelimit=1";
                        $content = getContent($url, $context, 10);
                        if ($content == false) {
                            $actives = 100;
                            break;
                        }
                        $content = json_decode($content, true);
                        if (isset($content["query"]["logevents"][0]))
                            if (strtotime($content["query"]["logevents"][0]["timestamp"]) > strtotime('-2 months'))
                                $actives += 1;
                    }
                if ($actives == 3)
                    break;
            }
            if ($actives == 0)
                $result[$wiki[0]] = [$wiki[1], 0, strval($total_sysop), strval($actives)];
            if ($actives == 1 || $actives == 2)
                $result[$wiki[0]] = [$wiki[1], 1, strval($total_sysop), strval($actives)];
            if ($actives == 3)
                $result[$wiki[0]] = [$wiki[1], 2, strval($total_sysop), "3+"];
        }
    }
}
catch (Exception $e) {}

$file = fopen("/data/project/swviewer/public_html/lists/activeSysops.txt", "w+");
fwrite($file, json_encode($result));
fclose($file);

function getContent($url, $context, $sec) {
    $cont = false; $i = 0;
    while ($cont == false && $i < 10) {
        if ($i !== 0) usleep($sec * 1000000);
        $cont = file_get_contents($url, false, $context);
        $i++;
    }
    return $cont;
}

function replace_wiki_name($wiki)
{
    $wiki = str_replace("_", "-", $wiki);
    $wiki = preg_replace("/^(.*)?wikimedia$/i", "$1.wikimedia.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikibooks$/i", "$1.wikibooks.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikiquote$/i", "$1.wikiquote.org", $wiki);
    $wiki = preg_replace("/^(.*)?wiktionary$/i", "$1.wiktionary.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikisource$/i", "$1.wikisource.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikivoyage$/i", "$1.wikivoyage.org", $wiki);
    $wiki = preg_replace("/^(.*)?mediawikiwiki$/i", "mediawiki.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikinews$/i", "$1.wikinews.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikiversity$/i", "$1.wikiversity.org", $wiki);
    $wiki = preg_replace("/^(.*)?wikimaniawiki$/i", "wikimania.wikimedia.org", $wiki);
    $wiki = preg_replace("/^(.*)?outreachwiki$/i", "outreach.wikimedia.org", $wiki);
    $wiki = preg_replace("/^(.*)?testcommonswiki$/i", "test-commons.wikimedia.org", $wiki);
    $wiki = preg_replace("/^(.*)?testwikidatawiki$/i", "test.wikidata.org", $wiki);
    $wiki = preg_replace("/^(.*)?testwiki$/i", "test.wikipedia.org", $wiki);
    $wiki = preg_replace("/^(.*)?incubatorwiki$/i", "incubator.wikimedia.org", $wiki);
    $wiki = preg_replace("/^(.*)?wiki$/i", "$1.wikipedia.org", $wiki);
    return $wiki;
}