<?php
abstract class BaseEntity{

	public $attributes;
	/**
     * @var Repository
     */
	protected $repository;
	public $logicDelete = false;
	public $saveWall = false;
	protected $icon = null;
	protected $entityIcon = null;
	public $skipIsDeletedFilter = false;
	protected $db_host = 'localhost';
	protected $db_name = 'zammerpadel';
	protected $db_user = 'root';
	protected $db_pass = '123456';
	protected $db_table = null;
	
	function __construct($id = 0) {
		if($this->db_table == null){
			$this->db_table = get_class($this);
		}
		$this->repository = Repository::getInstance($this->db_host, $this->db_user, $this->db_pass, $this->db_name);
		if($id > 0){
			$this->get($id);
		} else {
        	$this->attributes = new Attributes();
		}
    }

	public function getAll($orderBy = '',$limit = 0,  $offset = 0, $count = false) {
		if($count){
			$sql = "Select count(*) as count from " . $this->db_table . " where " . $this->getIsDeletedFilterQuery();
			return $this->repository->getValue($sql, "count");
		}else{

			$limOff = $this->repository->getLimitOffset($limit, $offset);
			$orderBy = $this->repository->getOrderBy($orderBy);
			$sql = "Select * from " . $this->db_table . " where " . $this->getIsDeletedFilterQuery() . $orderBy . $limOff;
			return $this->repository->fetchAllObject($sql, get_class($this));
		}
	}

	public function get($id) {
		$sql = "Select * from " . $this->db_table . " where id = " . $id;
		$this->attributes = $this->repository->fetchObject($sql, "Attributes");
	}

	public function delete($id = 0,$canEdit = true){
		if (!$id && isset($this->attributes) && $this->attributes && $this->attributes->id > 0){
			$id = $this->attributes->id;
			$this->attributes = new Attributes();
		}

		$class = get_class($this);
		$entity = new $class($id);
		$this->attributes = $entity->attributes;
		if(($canEdit && $this->canEdit()) || !$canEdit){
			if($id){
				if($this->saveWall){
					$wall = new Wall();
					$wall->getByContainer($id,get_class($this));
					$wall->delete();
				}

				$this->deleteCacheById($id);
				if(!$this->logicDelete){
					$sql = "Delete from " . $this->db_table . " where id = " . $id;
					$this->repository->query($sql);
					$this->attributes->id = $id;
// 					$this->deleteIcons();

					return true;
				}else{
					$sql = "UPDATE " . $this->db_table . " SET isDeleted = 1 where id = " . $id;
					$this->repository->query($sql);
					return true;
				}
			}
		}else{
			logError(langEcho("permission:delete:denied"));
		}
		return false;
	}

	public function updateField($column, $value, $checkPermission = true, $raiseEvents = true){
		return $this->updateFields(array($column => $value), $checkPermission, $raiseEvents);
	}

	public function updateFields($values, $checkPermission = true, $raiseEvents = true){
		if ($this->attributes->id > 0){
			$className = get_class($this);
			$this->previousData = new $className($this->attributes->id);
		}


		if ($this->attributes->id > 0){
			if(!$checkPermission || $this->canEdit()){
				$this->deleteCacheById();

				$sql = "Update " . $this->db_table . " SET ";

				$first = true;
				foreach($values as $key => $value){
					if ($key != "id"){
						$this->attributes->$key = $value;
						if($first){
							$sql .= $key . " = " . "'" . $this->repository->escape($value) . "'";
							$first = false;
						}else{
							$sql .= ", " . $key . " = " . "'" . $this->repository->escape($value) . "'";
						}
					}
				}
				$sql .= " where id = " . $this->attributes->id;
				$this->repository->query($sql);
			}else{
				logError(langEcho("permission:delete:denied"));
				return false;
			}

			if ($raiseEvents && method_exists($this, "OnUpdated") ){
				$this->OnUpdated();
			}
		}else{
			return false;
		}

		return true;
	}

	public function save($icon = true, $checkPermission = true, $raiseEvents = true, $validateRequired = "validateRequired"){

		if ($this->attributes->id > 0){
			$className = get_class($this);
			$this->previousData = new $className($this->attributes->id);
		}

		if ($validateRequired != "" && method_exists($this, $validateRequired) ){
			if (!$this->$validateRequired()){
				return false;
			}
		}

		if ($this->attributes->id > 0){
			if(!$checkPermission || $this->canEdit()){

				$this->deleteCacheById();

				$sql = "Update " . $this->db_table . " SET ";

				$first = true;
				foreach($this->attributes->data as $key => $value){
					if ($key != "id"){
						if($first){
							$sql .= $key . " = " . "'" . $this->repository->escape($value) . "'";
							$first = false;
						}else{
							$sql .= ", " . $key . " = " . "'" . $this->repository->escape($value) . "'";
						}
					}
				}
				$sql .= " where id = " . $this->attributes->id;
				$this->repository->query($sql);

				if($this->saveWall){
					$wall = new Wall();
					$wall->getByContainer($this->attributes->id,get_class($this));
					if(!$wall->attributes){
						$wall->attributes = new Attributes();
						$wall->attributes->containerId = $this->attributes->id;
						$wall->attributes->containerType = get_class($this);
					}
					$wall->attributes->date = time();
					$wall->save(false);
				}

				if ($raiseEvents && method_exists($this, "OnUpdated") ){
					$this->OnUpdated();
				}
			}else{
				logError(langEcho("permission:delete:denied"));
				return false;
			}
		}else{
			$sql = "Insert into " . $this->db_table . " (";

			$keys = array();$values = array();
			foreach($this->attributes->data as $key => $value){
				if ($key != "id"){
					$keys[] = $key;
					$values[] = $value;
				}
			}

			$first = true;
			foreach ($keys as $key){
				if($first){
					$sql .= $key;
					$first = false;
				}else{
					$sql .= "," . $key;
				}
			}

			$sql .= ") values (";

			$first = true;
			foreach ($values as $value){
				if($first){
					$sql .= "'" . $this->repository->escape($value) . "'";
					$first = false;
				}else{
					$sql .= ",'" . $this->repository->escape($value) . "'";
				}
			}

			$sql.= ")";

			$this->repository->query($sql);
			$this->attributes->id = $this->repository->getLastId();

			if($this->saveWall){
				$wall = new Wall();
				$wall->attributes->containerId = $this->attributes->id;
				$wall->attributes->containerType = get_class($this);
				$wall->attributes->date = time();
				$wall->save(false);
			}

			if ($raiseEvents && method_exists($this, "OnInserted") ){
				$this->OnInserted();
			}
		}

		if($icon){
			return $this->saveIcon();
		}

		return true;
	}

	function getRelatedObject($id, $className){
		return new $className($id);
	}

	function getRelatedObjects($keyValues, $className,$orderBy = '', $limit = 0, $offset = 0, $count = false){
		foreach($keyValues as $key => $value){
			if(!isset($where)){
				$where = " where " . $key . " = '" . $value . "' ";
			}else{
				$where .= " and " . $key . " = '" . $value . "' ";
			}
		}

		if($count){
			$sql = "Select count(*) as count from " . $className . $where;
			return $this->repository->getValue($sql, "count");
		}else{
			$limOff = $this->repository->getLimitOffset($limit, $offset);
			$orderBy = $this->repository->getOrderBy($orderBy);

			if(isset($where)){
				$where .= " and " . $this->getIsDeletedFilterQuery();
			}else{
				$where = $this->getIsDeletedFilterQuery();
			}

			$sql = "Select * from " . $className . $where . $orderBy . $limOff;
			return $this->repository->fetchAllObject($sql, $className);
		}

	}

	function getRelatedObjectsJoined($keyValues, $className, $joinedTable="", $joinedTableFilterLogicDelete=false ,$orderBy = '', $limit = 0, $offset = 0, $count = false, $repeatedValues = array()){
		$join = "";

		if ($joinedTable != ""){
			$join = " JOIN $joinedTable as j on j.id = $className.id$joinedTable";

			if ($joinedTableFilterLogicDelete){
				$join .= " AND j.isDeleted = 0 ";
			}
		}

		$columnNames = $this->getColumnNames($joinedTable);
		$selectColumnsJoin = "";

		foreach($columnNames as $columnName){
			if ($columnName != "id"){

				if (in_array($columnName, $repeatedValues)){
					$selectColumnsJoin .= ",j." . $columnName . " as $columnName" . "Joined ";
				}
				else{
					$selectColumnsJoin .= ",j." . $columnName;
				}
			}
		}


		foreach($keyValues as $key => $value){
			if(!isset($where)){
				$where = " where " . $key . " = '" . $value . "' ";
			}else{
				$where .= " and " . $key . " = '" . $value . "' ";
			}
		}

		if($count){
			$sql = "Select count(*) as count from " . $className . $join . $where;
			return $this->repository->getValue($sql, "count");
		}else{
			$limOff = $this->repository->getLimitOffset($limit, $offset);
			$orderBy = $this->repository->getOrderBy($orderBy);
			$sql = "Select $className.* $selectColumnsJoin from " . $className . $join . $where . $orderBy . $limOff;


			return $this->repository->fetchAllObject($sql, $className);
		}

	}

	function getObjectsRelatedAdminUserJoined($keyValues, $className, $orderBy = '', $limit = 0, $offset = 0, $count = false){

		$join = " JOIN EntityAdministrator ea on ea.containerId = $className.id JOIN User as u on u.id = ea.idUser ";

		$columnNames = $this->getColumnNames("User");
		$selectColumnsJoin = "";

		foreach($columnNames as $columnName){
			if ($columnName != "id"){
				$selectColumnsJoin .= ",u." . $columnName;
			}
		}

		foreach($keyValues as $key => $value){
			if(!isset($where)){
				$where = " where " . $key . " = '" . $value . "' ";
			}else{
				$where .= " and " . $key . " = '" . $value . "' ";
			}
		}

		if($this->logicDelete){
			if (isset($where)){
				$where .= " and $className.isDeleted = 0";
			}
			else{
				$where = " where $className.isDeleted = 0";
			}
		}

		if($count){
			$sql = "Select count(*) as count from " . $className . $where;
			return $this->repository->getValue($sql, "count");
		}else{
			$limOff = $this->repository->getLimitOffset($limit, $offset);
			$orderBy = $this->repository->getOrderBy($orderBy);
			$sql = "Select $className.* $selectColumnsJoin from " . $className . $join . $where . $orderBy . $limOff;

			return $this->repository->fetchAllObject($sql, $className);
		}
	}


	function search($where,$orderBy='', $limit = 0, $offset = 0, $count = false){
		if(isset($where) && $where != ""){
			$where .= " and " . $this->getIsDeletedFilterQuery();
		}else{
			$where = $this->getIsDeletedFilterQuery();
		}
		if($count){
			$sql = "Select count(*) as count from " . $this->db_table . " where " . $where;
			return $this->repository->getValue($sql, "count");
		}else{
			$limitOff = $this->repository->getLimitOffset($limit, $offset);
			$orderBy = $this->repository->getOrderBy($orderBy);
			$sql = "Select * from " . $this->db_table . " where " . $where . $orderBy . $limitOff;
			return $this->repository->fetchAllObject($sql, get_class($this));
		}
	}

	function getByPattern($where){
		if(isset($where)){
			$where .= " and " . $this->getIsDeletedFilterQuery();
		}else{
			$where = $this->getIsDeletedFilterQuery();
		}
		$sql = "Select * from " . $this->db_table . " where " . $where;
		$this->attributes = $this->repository->fetchObject($sql, "Attributes");
	}

	function insertFieldsFromRequest($validateRequired = "validateRequired",$raiseEvent = true){
		if(!$this->attributes->id > 0){
			$sql = "DESCRIBE " . $this->db_table;
			$resource = $this->repository->query($sql);
			while($row = $this->repository->fetch($resource)){
				$this->attributes->$row["Field"] = "";
			}
		}
		foreach($this->attributes->data as $key => $value){
			if ($key != "id" && isset($_REQUEST[$key])){
				$this->attributes->$key = $_REQUEST[$key];
			}
		}

		return $this->save(true,true,$raiseEvent,$validateRequired);
	}

	function getEntityType(){
		return get_class($this);
	}

	function deleteEntityIcon(){
		$entityIcon = $this->getEntityIcon();
		if($entityIcon){
			if(CACHE_ENTITY){
				$key = "EntityIcon_entityType_" . $this->getEntityType() . "_entityId_" . $this->attributes->id;
				$cache = Cache::getCacheInstance();
				$cache->delete($key);
			}
			$entityIcon->delete();
			$this->entityIcon = null;
		}
	}

	function getEntityIcon(){

		if($this->entityIcon == null){
			if($this->attributes && $this->attributes->id != "" && $this->attributes->id > 0){

				if(CACHE_ENTITY){
					$key = "EntityIcon_entityType_" . $this->getEntityType() . "_entityId_" . $this->attributes->id;
					$cache = Cache::getCacheInstance();
					$entityIcon = $cache->fetch($key);
					if($entityIcon && $entityIcon->attributes){
						$this->entityIcon = $entityIcon;
						return $entityIcon;
					}
				}

				$entityIcon = new EntityIcon();
				$entityIcon->getByPattern("entityType = '".$this->getEntityType()."' AND entityId = ".$this->attributes->id);

				if($entityIcon->attributes){
					if(CACHE_ENTITY){
						$cache->store($key, $entityIcon);
					}
					$this->entityIcon = $entityIcon;
					return $entityIcon;
				}
			}
			return false;
		}else{
			return $this->entityIcon;
		}
	}

	function getIconRow(){
		$entityIcon = $this->getEntityIcon();
		if($entityIcon){
			$icon = $this->getIconByPattern($entityIcon->attributes->iconId);
			if($icon->attributes && $icon->attributes->id != "" && $icon->attributes->id > 0){
				return $icon;
			}
		}
		return false;
	}

	function getIconByPattern($iconId){
		if($this->icon == null){

			if(CACHE_ENTITY){
				$key = "Icon_folder_" . $this->getEntityType() . "_id_" . $iconId;
				$cache = Cache::getCacheInstance();
				$icon = $cache->fetch($key);
				if($icon && $icon->attributes){
					$this->icon = $icon;
					return $icon;
				}
			}

			$icon = new Icon();
			$icon->getByPattern("id = " . $iconId . " and folder='" . $this->getEntityType() . "'");


			if($icon->attributes){
				if(CACHE_ENTITY){
					$cache->store($key, $icon);
				}
				$this->icon = $icon;
				return $icon;
			}

			return false;
		}else{
			return $this->icon;
		}
	}

	function getPathForIcon($iconId,$size){
		$icon = $this->getIconByPattern($iconId);
		if($icon->attributes && $icon->attributes->id != "" && $icon->attributes->id > 0){
			return $icon->attributes->folder . "/" . $icon->attributes->id . "/icon" . $size . $icon->attributes->iconExtension;
		}

		return false;
	}

	function getFolderForIcon($iconId){
		$icon = $this->getIconByPattern($iconId);
		if($icon->attributes && $icon->attributes->id != "" && $icon->attributes->id > 0){
			return $icon->attributes->folder . "/" . $icon->attributes->id . "/";
		}

		return false;
	}

	function getIconPath($size = "list"){
		$entityIcon = $this->getEntityIcon();
		if($entityIcon){
			return $this->getPathForIcon($entityIcon->attributes->iconId,$size);
		}
		return false;
	}

	function getIconFolder(){
		$entityIcon = $this->getEntityIcon();
		if($entityIcon){
			return $this->getFolderForIcon($entityIcon->attributes->iconId);
		}
		return false;
	}

	function getDefaultIconPath($size = "list"){
		return WWWPATHROOT . "images/" . $this->getEntityType() . "/icon".$size. DEFAULTIMAGEEXTENSION;
	}

	function hasIcon($size = "list"){
		return ($this->getIconPath($size));
	}

	function hasDefaultIcon($size = "list"){
		return file_exists($this->getDefaultIconPath($size));
	}

	function getIcon($size = 'list', $basePath = UPLOADWWW){
		if ($this->hasIcon($size)){
			$icon = $this->getIconRow();
			$created = "";
			if ($this->attributes->created){
				$created = "?" . $this->attributes->created;
			}
			return $basePath . $icon->attributes->folder . "/" . $icon->attributes->id . "/icon". $size . $icon->attributes->iconExtension . $created;
		}else{
			return WWWROOT .  "images/" . get_class($this) . "/icon". $size . DEFAULTIMAGEEXTENSION;
		}
	}

	function getColumnNames($className){
		$sql = "DESCRIBE " . $className;
		$resource = $this->repository->query($sql);

		$columnNames = array();

		while($row = $this->repository->fetch($resource)){
			$columnNames[] = $row["Field"];
		}

		return $columnNames;
	}

	function deleteIcons(){
// 		switch(UPLOADTO){
// 			case UPLOADLOCAL:

// 				if($this->attributes && $this->attributes->id != "" && file_exists(UPLOADPATH . get_class($this) . "/" . $this->attributes->id)){
// 					rrmdir(UPLOADPATH . get_class($this) . "/" . $this->attributes->id);
// 				}
// 				break;
// 			case UPLOADAMAZONS3:
// 				if($this->attributes && $this->attributes->id != ""){
// 					amazons3_delete_folder(AMAZONS3BUCKET, AMAZONS3MAINFOLDER . get_class($this) . "/" . $this->attributes->id . "/");
// 				}
// 				break;
// 		}

	}

	function createIcon($iconExtension = DEFAULTIMAGEEXTENSION, $title = "", $mimeType = ""){

		// delete previous version of the entity icon if exists, only from db
		$this->deleteEntityIcon();

		$icon = new Icon();
		$icon->attributes->folder = $this->getEntityType();
		$icon->attributes->created = time();
		$icon->attributes->iconExtension = $iconExtension;
		$icon->attributes->title = $title;
		$icon->attributes->mimeType = $mimeType;
		if(!$icon->save(false, false)){
			return false;
		}

		$this->icon = null;

		$entityIcon = new EntityIcon();
		$entityIcon->attributes->iconId = $icon->attributes->id;
		$entityIcon->attributes->entityType = $this->getEntityType();
		$entityIcon->attributes->entityId = $this->attributes->id;
		return $entityIcon->save(false);
	}


    function associateIcon($iconId){
        // delete previous version of the ebtity icon if exists, only from db
        $this->deleteEntityIcon();

        $entityIcon = new EntityIcon();
        $entityIcon->attributes->iconId = $iconId;
        $entityIcon->attributes->entityType = $this->getEntityType();
        $entityIcon->attributes->entityId = $this->attributes->id;
        return $entityIcon->save(false);
    }

	function saveIconSize(){
		if ($this->hasIcon("flash")){
			$icon = $this->getIconRow();
			$size = getFileSize($this->getIconPath("flash"));
			$sql = "UPDATE Icon set size = " . $size. " where id=" . $icon->attributes->id . " AND folder = '" . $this->getEntityType() . "'";
			if(CACHE_ENTITY){
				$key = "Icon_folder_" . $this->getEntityType() . "_id_" . $icon->attributes->id;
				$cache = Cache::getCacheInstance();
				$cache->delete($key);
			}
			$this->repository->query($sql);
		}
	}

	function saveIcon(){
		if (isset($_FILES['icon']) && $_FILES['icon']['error'] == 0){

			if(validateImageFile($_FILES['icon']["tmp_name"])){
				if($this->createIcon()){
					saveUploadFile($this->getIconPath("original"), file_get_contents($_FILES["icon"]["tmp_name"]));
					saveUploadFile($this->getIconPath("flash"), get_resized_image_from_existing_file(UPLOADWWW.$this->getIconPath("original"),1024,768, false));
					saveUploadFile($this->getIconPath("list"), get_resized_image_from_existing_file(UPLOADWWW.$this->getIconPath("original"),236,177, false));
					saveUploadFile($this->getIconPath("ios"), get_resized_image_from_existing_file(UPLOADWWW.$this->getIconPath("original"),960,640, false));
					$this->updateIconVersion();
					$this->saveIconSize();
				}else{
					logError(langEcho("could:not:create:icon"));
					$this->delete();
					return false;
				}
			}else{
				logError(langEcho("not:an:image"));
				$this->delete();
				return false;
			}
		}
		return true;
	}

	function updateIconVersion($time = null){
		if(!isset($time)){
			$time = time();
		}
		$icon = $this->getIconRow();
		$sql = "UPDATE " . get_class($icon) . " set created = " . $time . " where id=" . $icon->attributes->id . " AND folder = '" . $this->getEntityType() . "'";
		if(CACHE_ENTITY){
			$key = "Icon_folder_" . $this->getEntityType() . "_id_" . $icon->attributes->id;
			$cache = Cache::getCacheInstance();
			$cache->delete($key);
		}
		$this->repository->query($sql);
	}

	function canEdit(){
		if(validateUserPermission(array(UserRole::$roleAdmin, UserRole::$roleSupport))){
			return true;
		}

		return false;
	}

	protected function canEditEntity($containerId, $containerType, $userId, $className){
		$result = $this->getRelatedObjects(array("containerId" => $containerId, "containerType" => $containerType, "idUser" => $userId),$className,"",0,0,true);

		if($result>0)
			return true;
		else
			return false;
	}

	function getLastId(){
		$sql = "SELECT max(id) as max from " . $this->db_table;
		return $this->repository->getValue($sql, "max");
	}

	protected function getIsDeletedFilterQuery(){
		if(!$this->skipIsDeletedFilter && $this->logicDelete){
			return " isDeleted = 0 ";
		}

		return " 1=1 ";
	}

	function deleteEntityAdministratoByContainerIdAndType($containerId, $containerType){
		$sql = "Delete from " . $this->db_table . " where containerId = " . $containerId . " AND containerType = '" . $containerType . "'";
		$this->repository->query($sql);
	}
}
