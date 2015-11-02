<?php

const BR = '<br>';
const TOR_PATH = '/arc/tor/';
const SOURCE_PATH = '/arc/vids/';
const DEST_PATH = '/arc/upl/vids/';
const DESCRIPTIONS = '/arc/post/description.txt';
const TITLES = '/arc/post/title.txt';
const TAGS = '/arc/post/tags.txt';
//const POSTS_BBCODE = '/arc/post/posts-bbcode.csv';
//const POSTS_HTML = '/arc/post/posts-html.csv';



function bbcode_to_html($bbtext){
  $bbtags = array(
    "[b]" => "<span style='font-weight:bold;'>",  "[/b]" => "</span>",
    "[i]" => "<span style='font-weight:bold;'>",  "[/i]" => "</span>",
    "[u]" => "<span style='text-decoration:underline;'>",  "[/u]" => "</span>",
  );

  $bbtext = str_ireplace(array_keys($bbtags), array_values($bbtags), $bbtext);

  $bbextended = array(
    "/\[url](.*?)\[\/url]/i" => "<a href='http://$1' title='$1'>$1</a>",
    "/\[url=(.*?)\](.*?)\[\/url\]/i" => "<a href='$1' title='$1'>$2</a>",
    "/\[img](.*?)\[\/img]/i" => "<img src='$1' alt='$1'>" );

  foreach($bbextended as $match=>$replacement){
    $bbtext = preg_replace($match, $replacement, $bbtext);
  }
  return $bbtext;
}


function clean_new_filename($str){
    $mask1 = array("[", "]", "{", "}", "~", "(", ")", "Empornium", "'", "`", ";", "!", "?", ":");
    $mask2 = array("-", "_", "#", ".com");
    $mask3 = array("&");
    $mask4 = array("    ");
    $mask5 = array("   ");
    $mask6 = array("  ");

    $result = str_replace($mask1, "", $str);
    $result = str_replace($mask2, " ", $result);
    $result = str_replace($mask3, "and", $result);
    $result = str_replace($mask4, " ", $result);
    $result = str_replace($mask5, " ", $result);
    $result = str_replace($mask6, " ", $result);

    return $result;
}



function clean_string($str){
	$mask = array(".mp4",".flv",".FLV",".avi",".mpg",".mpeg",".wmv",".mkv",".torrent.added");
	$result = str_replace($mask, "", $str);
	return $result;
}

function dir_to_array ($directory){

	$handle = opendir($directory);

	$dir_array = array();

	while (($file = readdir($handle)) !== false) if ($file!='.' && $file!='..') $dir_array[]=$file;
	
	closedir($handle);
	
	return $dir_array;
}

function dir_to_asoc_array ($directory){
	$dir_handle = opendir($directory);

	$dir_array = array();

	while (($file = readdir($dir_handle)) !== false) 


		if ($file!='.' && $file!='..'){
			
			$file_handle = fopen($directory.$file, 'r');
			$filestr = fread($file_handle, filesize($directory.$file));
			$dir_array[clean_string($file)]=$filestr;
			fclose($file_handle);
			
	}
		
	closedir($dir_handle);
	
		//****************************************
//			var_dump($dir_array);		
		//****************************************

	return $dir_array;
}

function get_string_from_torr($torr){
	
	$file_handle = fopen($torr, 'r');
	$filestr = fread($file_handle, filesize($torr));
	fclose($file_handle);
	return $filestr;

}




class Bencode
{

    /**
* The version of the library.
*
* @var string
*/
    const VERSION = "1.0";

    /**
* Bencodes the given data structure.
*
* @param mixed
* @return string
* @throws Exception
*/
    public static function encode($value)
    {
        if (is_null($value)) {
            return "0:";
        }
        if (is_int($value)) {
            return "i" . $value . "e";
        }
        if (is_string($value)) {
            return strlen($value) . ":" . $value;
        }
        if (is_array($value)) {
            if (self::isAssoc($value)) {
                ksort($value, SORT_STRING);
                $buffer = "d";
                foreach ($value as $key => $v) {
                    $buffer .= self::encode(strval($key));
                    $buffer .= self::encode($v);
                }
                $buffer .= "e";
            } else {
                ksort($value, SORT_NUMERIC);
                $buffer = "l";
                foreach ($value as $v) {
                    $buffer .= self::encode($v);
                }
                $buffer .= "e";
            }
            return $buffer;
        }

        throw new Exception("Unable to encode data type: " . gettype($value));
    }

    /**
* Decodes the given string. The second parameter is only used in recursion.
*
* @param string
* @param int
* @return mixed
* @throws Exception
*/
    public static function decode($tokens, &$i=0)
    {
        if (is_string($tokens)) {
            $tokens = str_split($tokens);
        }

        switch ($tokens[$i]) {
        case "d":
            $dict = array();
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == "e") {
                    return $dict;
                } else {
                    $key = self::decode($tokens, $i);
                    if (isset($tokens[++$i])) {
                        $dict[$key] = self::decode($tokens, $i);
                    } else {
                        throw new Exception("Dictionary key ($key) without a value at index $i");
                    }
                }
            }
            throw new Exception("Unterminated dictionary at index $i");

        case "l":
            $list = array();
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == "e") {
                    return $list;
                } else {
                    $list[] = self::decode($tokens, $i);
                }
            }
            throw new Exception("Unterminated list at index $i");

        case "i":
            $buffer = '';
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == "e") {
                    return intval($buffer);
                } elseif (ctype_digit($tokens[$i])) {
                    $buffer .= $tokens[$i];
                } else {
                    throw new Exception("Unexpected token while parsing integer at index $i: {$tokens[$i]}");
                }
            }
            throw new Exception("Unterminated integer at index $i");

        case ctype_digit($tokens[$i]):
            $length = $tokens[$i];
            while (isset($tokens[++$i])) {
                if ($tokens[$i] == ":") {
                    break;
                } elseif (ctype_digit($tokens[$i])) {
                    $length .= $tokens[$i];
                } else {
                    throw new Exception("Unexpected token while parsing string length at index $i: {$tokens[$i]}");
                }
            }
            $end = $i + intval($length);
            $buffer = '';
            while (isset($tokens[++$i])) {
                if ($i <= $end) {
                    $buffer .= $tokens[$i];
                    if ($i == $end) {
                        return $buffer;
                    }
                }
            }
            throw new Exception("Unterminated string at index $i");
        }

        throw new Exception("Unexpected token at index $i: {$tokens[$i]}");
    }

    /**
* Tells whether an array is associative or not. In order to be non-associative,
* each of the array's key numbers must correspond exactly to it's position
* in the array.
*
* @param array
* @return bool
*/
    public static function isAssoc($array)
    {
        return count($array) !== array_reduce(array_keys($array), array("Bencode", "isAssocCallback"), 0);
    }

    /**
* A callback function used by {@link isAssoc()}.
*
* @return int
*/
    protected static function isAssocCallback($a, $b)
    {
        return $a === $b ? $a + 1 : 0;
    }

}

function bencode($value)
{
    return Bencode::encode($value);
}

function bdecode($value)
{
    return Bencode::decode($value);
}

function login($url,$login,$pass){
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $url);
   curl_setopt($ch, CURLOPT_REFERER, $url);
   curl_setopt($ch, CURLOPT_VERBOSE, 1);
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,"username=".$login."&password=".$pass."&keeplogged=1&login=Login");
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:30.0) Gecko/20100101 Firefox/30.0");
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_COOKIEJAR, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');
   $result=curl_exec($ch);

   //curl_close($ch);

   return $ch;
}

function Read($url, $handle){
   //$ch = curl_init();
   curl_setopt($handle, CURLOPT_URL, $url);
   curl_setopt($handle, CURLOPT_REFERER, "http://torrents.empornium.me/index.php");
   curl_setopt($handle, CURLOPT_POST, 0);
   curl_setopt($handle, CURLOPT_COOKIEFILE, $_SERVER['DOCUMENT_ROOT'].'/cookie.txt');
   curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($handle, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:30.0) Gecko/20100101 Firefox/30.0");

   $result = curl_exec($handle);

   //curl_close($handle);

   return $result;
}

function find_all_substr_array($haystack, $from, $to){

$buffer="";
$result="";
$result_array=array();

while ($j<strlen($haystack)){

		while ($from[$i]==$haystack[$j+$i] && $i<=strlen($from)){
		$buffer.=$haystack[$j+$i];
		$i++;
	}
	
	if(strlen($buffer)==strlen($from)) {
		
		$result="";
		$i=0;
		$start = $j+strlen($buffer);
		
		while($haystack[$start+$i]!=$to){
		
			$result.=$haystack[$start+$i];
			$i++;	
		}
	$result_array[]=$result;
	}
	
	$buffer = "";
	$i=0;	
	$j++;
	
	}

return $result_array;
}

function find_all_substr_string($haystack, $from, $to){

$buffer="";
$result="";
$result_string="";

while ($j<strlen($haystack)){

		while ($from[$i] == $haystack[$j+$i] && $i<=strlen($from)){
		$buffer .= $haystack[$j+$i];
		$i++;
	}
	
	if (strlen($buffer) == strlen($from)) {
		
		$result="";
		$i=0;
		$start = $j+strlen($buffer);
		
		while($haystack[$start+$i]!=$to){
		
			$result.=(($haystack[$start+$i]==".") ? " ":$haystack[$start+$i]);
			 
			$i++;	
		}
	$result_string.=$result.", ";
	}
	
	$buffer = "";
	$i=0;	
	$j++;
	
	}

return $result_string;
}

function gettags($tor_name){
	login( "http://torrents.empornium.me/login.php", "sergemitrof", "qxwv35azsc" );
	//$str=Read("http://torrents.empornium.me/torrents.php?order_by=time&order_way=desc&searchtext={$tor_name}&search_type=1&taglist=&tags_type=0");
	$tags = find_all_substr_string($str,"add_tag('","'")."<br>";
	return $tags;
}
