<?php

require 'functions.php';
require 'simple_html_dom.php';


login('http://www.empornium.me/login.php');

$str = get_page('http://www.empornium.me/torrents.php');

file_put_contents('page.html', $str);



$dom = file_get_html('page.html');

$elements = $dom->find('a[onmouseout="return nd();"]');
$titles = squeeze_plain_text($elements);

$elements = $dom->find('div.tags');
$tags = squeeze_plain_text($elements);

$elements = $dom->find('a[title="Download Torrent"]');
$urls = squeeze_href($elements);

$inf_table = array_map(
												function($title, $tag, $url){ 

														$tag = trim($tag);
														$url = 'http://empornium.me/' . htmlspecialchars_decode($url);

														return array(	'title' => $title, 
																					'tags'	=> $tag, 
																					'url'		=> $url	); 
												}, 

												$titles,
												$tags,
												$urls
											);	

print_r($inf_table);

save_torrent('http://empornium.me/torrents.php?action=download&id=295100&authkey=de60e4d4c914bf641f867a093ca036a9&torrent_pass=v9waltzqssnfjjrcm77tmevers0432gi', 
'[MyFriendsHotGirl] 2015-10-27 Gigi Allens [1080p][MP4]');

?>
