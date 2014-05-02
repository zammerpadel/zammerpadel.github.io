<?php

  class RepositoryHub {

	private $link = null;

	private static $instance = null;

	private function __construct() {
		$this->connect();
	}

	public static function getInstance() {
		if (!RepositoryHub::$instance)
            RepositoryHub::$instance = new RepositoryHub();
		return RepositoryHub::$instance;
	}

	public function connect () {
		$this->link = mysql_connect(HUB_DB_HOST, HUB_DB_USER, HUB_DB_PASSWORD);
		if (!$this->link) {
    			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db (HUB_DB_NAME);
	}

	public function close () {
		if ($this->link)
			mysql_close($this->link);
	}

	public function query ($sql) {
		if (!$this->link) {
			$this->connect();
		}

		//logException(new ErrorException($sql, 0, 0, "database query", 0));
// 		echo $sql ."<br/>";
		return mysql_query($sql,$this->link);
	}

	public function fetch ($resource) {
		return mysql_fetch_assoc ( $resource );
	}

  	public function fetchObject ($sql, $className) {
  		$resource = $this->query($sql);
		return mysql_fetch_object ( $resource,$className );
	}

  public function fetchArray ($sql) {
  		$resource = $this->query($sql);
  		$return = array();
  		while ($row = mysql_fetch_array ( $resource)){
  			$return[] = $row;
  		}
		return $return;
	}

	public function fetchStdClass ($sql) {
		$resource = $this->query($sql);
		$return = array();
		while ($row = mysql_fetch_object ( $resource, "stdClass")){
			$return[] = $row;
		}
		return $return;
	}

  public function fetchAllObject ($sql, $className) {
  		$resource = $this->query($sql);
  		$return = array();
  		while ($row = mysql_fetch_object ( $resource,"Attributes" )){
  			$class = new $className();
  			$class->attributes = $row;
  			$return[] = $class;
  		}
		return $return;
	}

	public function getValue($sql, $column){
		$resource = $this->query($sql);
		$row = $this->fetch($resource);
		if($row){
			return $row[$column];
		}
		return null;
	}

	public function getLastId(){
		if (!$this->link) {
			$this->connect();
		}
		return mysql_insert_id ($this->link);
	}

	public function escape($value){
		if (!$this->link) {
			$this->connect();
		}
		return mysql_real_escape_string($value,$this->link);
	}

	public function getLimitOffset($limit = 0, $offset = 0){
		if(!$limit && !$offset){
			return "";
		}
		if($limit && $offset){
			return " LIMIT " .$offset . ",". $limit;
		}
		if($limit){
			return " LIMIT 0,". $limit;
		}
		if($offset){
			return " LIMIT " .$offset. ",9999999999999";
		}

	}

	public function getOrderBy($orderBy = ''){
		if($orderBy){
			return " order by " . $orderBy;
		}
		return "";
	}

	public function getSearchSplitBySpace($field, $search, $operator = "AND"){
		$string = '';

	  	if($search){
		  	foreach (explode(' ', $search) as $s){
		  		if($string != ''){
		  			$string .= " " . $operator . " ";
		  		}
		  		$string .= " " . $field . " like '%" . mysql_real_escape_string($s) ."%' ";
		  	}
	  	}else{
	  		$string = "1 = 1";
	  	}
	  	return "(" . $string . ")";

	}

  	public static function getMysqlDateTime($date){
		return date( 'Y-m-d H:i:s', $date );
	}

	public static function getMysqlDate($date){
		return date( 'Y-m-d', $date );
	}
}