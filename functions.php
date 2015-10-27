<?php

function login($url){

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookiejar");
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "username=sergemitrof&password=qxwv35azsc&keeplogged=1&login=Login");

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$buf = curl_exec ($ch);

	curl_close ($ch);

	return $buf;
}


function get_page($url){

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiejar");
	curl_setopt($ch, CURLOPT_URL, $url);

	$buf = curl_exec ($ch);

	curl_close ($ch);

	return $buf;
}


function squeeze_plain_text($elements){
	$a = array();
	foreach($elements as $element){
		$a[] = $element->plaintext;
	}
	return $a;
}


function squeeze_href($elements){
	$a = array();
	foreach($elements as $element){
		$a[] = $element->href;
	}
	return $a;
}


function save_torrent($url, $torrent_file_name){
	$torrent_file_name = $torrent_file_name . ".torrent";
	$res = fopen("torrents/$torrent_file_name", "w+");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_FILE, $res);
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiejar");
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$buf = curl_exec($ch);
	curl_close($ch);

}


