<?php

$rank = '-';
$commits = 'Not listed';
$bgcol = "000000";
$strcol = "FFFFFF";
$txtcol = "FFFFFF";
$error = "";


function getCountry($country, $user)
{
    $cURLConnection = curl_init();

    curl_setopt($cURLConnection, CURLOPT_URL, 'https://committers.top/' . strtolower($country));
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($cURLConnection, CURLOPT_HEADERFUNCTION, "getHeaders");
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'Accept: application/vnd.github.v3+json',
        'User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/93.0.4577.82 Mobile Safari/537.36'
    ));

    $json = curl_exec($cURLConnection);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML($json);
    $list = $dom->getElementsByTagName("tbody")
        ->item(0);
    $data = $list->getElementsByTagName("tr");
    $date = $dom->getElementsByTagName("code")
        ->item(0)->nodeValue;
    foreach ($data as $n)
    {

        $elem = $n->getElementsByTagName("td");
        $rank = $elem->item(0)->nodeValue;
        $commits = $elem->item(2)->nodeValue;
        $username = $n->getElementsByTagName("a")
            ->item(0)->nodeValue;
		
        $icon = $elem->item(3)
            ->getElementsByTagName("img")
            ->item(0)
            ->getAttribute("data-src");
        $name = $elem->item(1)->nodeValue;
        $name = str_replace($username, "", $name);
        $name = str_replace("(", "", $name);
        $name = str_replace(")", "", $name);
        $name = str_replace('"', '\"', $name);
        
        if ($user == $username){
			$GLOBALS['commits'] = $commits;
			$GLOBALS['rank'] = str_replace(".", "", $rank);
			break;
		}

    }
}

if ($_GET['user'] && $_GET['country']){
	getCountry($_GET['country'], $_GET['user']);
	
	$country = ucfirst($_GET['country']);
	$user = $_GET['user'];
	
	if ($_GET['bg_color']){
		$bgcol = $_GET['bg_color'];
	}
	if ($_GET['text_color']){
		$txtcol = $_GET['text_color'];
	}
	if ($_GET['border_color']){
		$strcol = $_GET['border_color'];
	}
	
	$svg = file_get_contents("commits-top.svg");
	header('Content-Type: image/svg+xml');
	echo str_replace(['{{user}}', '{{Country}}', '{{commits}}', '{{rank}}', '{{bgcol}}', '{{strcol}}', '{{txtcol}}', '{{error}}'], [$user, $country, $commits, $rank, $bgcol, $strcol, $txtcol, $error], $svg);
}

?>
