<?php

require 'functions.php';
require 'simple_html_dom.php';
require 'Database.class.php';

login('http://www.empornium.me/login.php');

//$str = get_page('http://www.empornium.me/torrents.php');
$str = get_page('http://www.empornium.me/torrents.php?order_by=time&order_way=desc&searchtext=&search_type=0&taglist=jerk.off.instructions&tags_type=0');

file_put_contents('page.html', $str);

/*===============================================================================================*
 *																Get ALL page rows
/*===============================================================================================*/

$dom = file_get_html('page.html');
$rows = $dom->find('tr[class*="torrent row"]');

echo count($rows);
echo "\n";

/*===============================================================================================*
 *																Get file sizes inside the rows
/*===============================================================================================*/
$tds = array();
foreach($rows as $row)
		$tds[] = $row->children(5);

$file_sizes = squeeze_plain_text($tds);
//$file_sizes = array_map(function($e){return (float)$e;}, $file_sizes);

/*===============================================================================================*
 *																Get download URLs for torrents inside the rows
/*===============================================================================================*/
$urls = array();
foreach($rows as $row)
		$urls[] = 'http://empornium.me/' . $row->children(1)->find('a[href^=torrents.php?action=download]')[0]->href;



/*===============================================================================================*
 *																Get post titles inside the rows
/*===============================================================================================*/
$titles = array();
foreach($rows as $row)
		$titles[] = $row->children(1)->find('a[onmouseout=return nd();]')[0]->plaintext;



/*===============================================================================================*
 *																Get post tags inside the rows
/*===============================================================================================*/
$tags = array();
foreach($rows as $row)
		$tags[] = $row->children(1)->find('div.tags')[0]->plaintext;


$db = new Database\Database('sitecontent', 'root', 'qxwv35azsc');

$info_table = array_map( 'save_torrent_files', $titles, $tags, $urls, $file_sizes	);	

print_r($info_table);

array_filter($info_table, 'save_parsed_to_db');

?>
