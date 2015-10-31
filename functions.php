<?php

define('NUM_TORRENTS', 50);

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

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiejar");
	curl_setopt($ch, CURLOPT_URL, $url);

	$buf = curl_exec ($ch);

	curl_close ($ch);

	return $buf;
}


function squeeze_plain_text($elements){
	$i=1;
	$a = array();
	foreach($elements as $element){
		if($i>NUM_TORRENTS) break;
		$a[] = $element->plaintext;
		$i++;
	}
	return $a;
}


function squeeze_href($elements){
	$i=1;
	$a = array();
	foreach($elements as $element){
		if($i>NUM_TORRENTS) break;
		$a[] = $element->href;
		$i++;
	}
	return $a;
}


function save_torrent($url, $torrent_file_name){

	$torrent_file_name = 'torrents/' . $torrent_file_name;

	$res = fopen("$torrent_file_name", "w+");

	$ch = curl_init();


	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_FILE, $res);
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiejar");

	$buf = curl_exec($ch);
	curl_close($ch);

	fclose($res);

	//return hash_file('md5', $torrent_file_name);

}

function get_counter($i){

   if ($i<10) $counter="000".$i;
	   elseif ($i<100) $counter="00".$i;
	     elseif ($i<1000) $counter="0".$i;

	 return $counter;
}



function save_torrent_files($title, $tags, $url, $file_size){ 

 	global $i;
	
	$filename = preg_replace('/[^a-z0-9\._]/i', '-', $title);
	$filename = preg_replace('/-+/i', '-', $filename);
	$filename = trim($filename, '-') . '.torrent';

	$md5 = md5($filename);

	$filename = get_counter(++$i) . '_' . $md5 . '_' . $filename;

 	$tags = trim($tags);
 	$tags = preg_replace('/ /', ', ', $tags);
 	$tags = preg_replace('/\./', ' ', $tags);

 	$url = htmlspecialchars_decode($url);
 	save_torrent($url, $filename);

 	return array(	'title'		  => $title, 
								'file_name'  => $filename,
 								'tags'      => $tags, 
 								'url'       => $url,
 								'md5'       => $md5,
								'file_size' => $file_size
 							); 
}


function save_parsed_to_db($row){
									global $db;
									$db->append($row);
							}
