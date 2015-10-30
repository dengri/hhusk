<?php namespace Database;

class Database{

	private $con;

	function __construct($db, $login, $password){
	
		try{
		
			$this->con = new \PDO("mysql:host=localhost;dbname=$db", $login, $password);
			
			$this->con->exec("SET NAMES 'utf8'");
			$this->con->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		
		}catch(\PDOException $e){
		
			echo $e->getMessage();
			die('Something is wrong!');
		
		}
	}

	function getConnection(){
		return $this->con;	
	}

	function saveFileInfo(){

		$qstring = "INSERT INTO torrents (title, tags, urls) VALUES($title, $tag, $url)";

		return $con->query($qstring);	
	}
}



