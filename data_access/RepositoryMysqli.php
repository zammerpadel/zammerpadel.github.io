<?php
class RepositoryMysqli
{

    public static $hub_instance = null;
    public static $ct_instance = null;
    private $server ='';
    private $connection;
    private $bindings = array();
    public $stmt;

    private function __construct($server) {
        $this->server = $server;
        if($this->server=='hub')
        {
            $host = HUB_DB_HOST;
            $user = HUB_DB_USER;
            $password = HUB_DB_PASSWORD;
            $name = HUB_DB_NAME;
        }else if($this->server=='ct')
        {
            $host = DB_HOST;
            $user = DB_USER;
            $password = DB_PASSWORD;
            $name = DB_NAME;
        }
        $this->connect($host,$user,$password,$name);
    }

    public static function getInstance($server) {
        if($server=='hub')
        {
            if (!RepositoryMysqli::$hub_instance)
            {
                RepositoryMysqli::$hub_instance = new RepositoryMysqli($server);
            }
            return RepositoryMysqli::$hub_instance;
        }else if($server=='ct')
        {
            if (!RepositoryMysqli::$ct_instance)
            {
                RepositoryMysqli::$ct_instance = new RepositoryMysqli($server);
            }
            return RepositoryMysqli::$ct_instance;
        }
    }

    private function connect($host,$user,$password,$name){
        $this->connection = new mysqli($host, $user, $password, $name);
    }

    public function prepare($query)
    {
        return $this->connection->prepare($query);
    }

    public function __get($name) {
        return $this->connection->$name;
    }
}
