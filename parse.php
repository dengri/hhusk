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

$i = 0;

$info_table = array_map(
												function($title, $tag, $url){ 

														global $i;

														$counter = get_counter(++$i); 

														if($i>3) return;

														$tag = trim($tag);
														$tag = preg_replace('/ /', ', ', $tag);
														$tag = preg_replace('/\./', ' ', $tag);
														$url = 'http://empornium.me/' . htmlspecialchars_decode($url);
														
														$md5 = save_torrent($url, $counter . '_' . $title);

														return array(	'title' => $title, 
																					'tags'	=> $tag, 
																					'url'		=> $url,
																					'md5'		=> $md5
																				); 
												}, 

												$titles,
												$tags,
												$urls
											);	

print_r($info_table);

?>
