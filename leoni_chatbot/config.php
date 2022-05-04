<?php
/** 
+ About: Configuration file 
**/

//Require lib to use environment variables 
require_once('../../phpdotenv/vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__,'../../.env');
$dotenv->load();

//Define variables from environment
define('DB_HOST',  $_ENV['DB_HOST']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_PORT', $_ENV['DB_PORT']);
define('AUTHORIZATION',$_ENV['AUTHORIZATION']);

  
//Setting parameters and Connection to DB
class DBConnection {

    protected $_db;
    public $host = DB_HOST;
    public $port = DB_PORT;
    public $database = DB_NAME;
    public $username = DB_USER;
    public $password = DB_PASSWORD;
    public $conect;
    public function __construct() {
        try {
            $dns = "mysql:host=$this->host;port=$this->port;
            dbname=$this->database";
			$opciones = array(
								PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
								$this->_db = new PDO($dns, DB_USER, DB_PASSWORD, $opciones); 			
			} catch (Exception $ex) {
					print "" . $ex->getMessage();
				die();
        }
        $this->conect = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }
}

//Setting cURL parameters
class curlParameters {
    public $auth = AUTHORIZATION;
}




