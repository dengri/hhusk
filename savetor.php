<?php

require 'functions.php';

function _save_torrent($tor_url, $tor_name, $login_url){
	$tor_name = 'torrents/' . $tor_name . ".torrent";
	$res = fopen($tor_name, 'w+');

	login($login_url); //'http://www.empornium.me/login.php');

	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookiejar');
	curl_setopt($ch, CURLOPT_FILE, $res);
	curl_setopt($ch, CURLOPT_URL, $tor_url); //'http://empornium.me/torrents.php?action=download&id=294898&authkey=de60e4d4c914bf641f867a093ca036a9&torrent_pass=v9waltzqssnfjjrcm77tmevers0432gi');
	$buf = curl_exec($ch);
	echo hash_file('md5', $tor_name);	
	curl_close($ch);

}


_save_torrent( 'http://empornium.me/torrents.php?action=download&id=294898&authkey=de60e4d4c914bf641f867a093ca036a9&torrent_pass=v9waltzqssnfjjrcm77tmevers0432gi',
							'test2',
							'http://www.empornium.me/login.php'
						);
