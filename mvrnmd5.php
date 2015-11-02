<?php

include "functions.php";
include "functions2.php";

$torrentsAsocArray = dir_to_asoc_array ("/arc/tor/");
$torrents_array = dir_to_array ("/arc/tor/");
$filesArray = dir_to_array("/arc/vids/");

$genre = file("/arc/scr/upldir", FILE_IGNORE_NEW_LINES)[0];
	
set_time_limit(3600);

$newFileNamesArray = array();

	foreach ($filesArray as $filename)
	{
		foreach ($torrentsAsocArray as $torrentFileName => $torrentContentString )
		{
			$searchResult = strpos($torrentContentString, $filename);

			if ($searchResult)
			{
				$newFileNamesArray[$filename] = clean_new_filename($torrentFileName." ".$filename); 
			//	$newFileNamesArray[$filename] = clean_new_filename($filename);

				continue 2;		
			}	
		}		
	}

	asort($newFileNamesArray);
	
	$counter="";
	$i=0;


	foreach ($newFileNamesArray as $key => $value)
	{

		$i++;

		if ($i<10) $counter="000".$i;
			elseif ($i<100) $counter="00".$i;
				elseif ($i<1000) $counter="0".$i;

		$newFileNamesArray[$key] = $counter. "_" . $genre . "_xxx_video_". substr($value, strlen($value)-5);
		//$newFileNamesArray[$key] = $counter . "_" . $value;	
	

		$titles .= $counter . "_ " . substr($value, 0, strlen($value)-5) . "\n";
	}
		
file_put_contents("/arc/upl/inf/titles.txt", $titles);
	
	foreach ($newFileNamesArray as $key => $value)
	{
		rename(SOURCE_PATH . $key, DEST_PATH . $value);
		echo DEST_PATH . $key . "----->\n";
		echo DEST_PATH . $value . "\n\n";
	}


