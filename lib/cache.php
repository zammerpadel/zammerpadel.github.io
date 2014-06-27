<?php

class Cache {

	private static $keySeparator = "#";
	/**
	 * The Memcached set of servers
	 * @var Memcache
	 */
	private $memcache;

	/**
	 * The time in seconds the cache entries will live.
	 * @var int
	 */
	private $lifeTime;

	/**
	 * Creates a new cache manager.
	 *
	 * @param array $serversData An array of servers config.
	 * @param int $lifeTime The time in seconds the cache entries will live.
	 */
	public function __construct(Array $serversData, $lifeTime) {
		if (count($serversData) == 0) {
			throw new InvalidArgumentException('Cache enabled with no server');
		}

		$this->lifeTime = $lifeTime;
		$this->keys = array();
		$this->memcache = new Memcache();

		$default = array(
			'port' => 11211,
			'persistent' => true,
			'weight' => 1
		);

		foreach ($serversData as $server) {
			$server = array_merge($default, $server);
			if (! isset($server['host'])) {
				throw new Exception('No memcache host provided');
			}

			$status = $this->memcache->addServer(
			$server['host'],
			$server['port'],
			$server['persistent'],
			$server['weight']
			);

			if (! $status) {
				throw new Exception(
					'Could not connect to: ' . $server['host']
				);
			}
		}
	}

	/**
	 * Stores a value associated to the provided key
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function store($key, $value, $getActualKey = true) {
		if($getActualKey){
			$key = $this->getActualKey($key);
		}
		$success = $this->memcache->set($key,$this->encodeValue($value),0,$this->getActualExpirationTime());
		if(!$success){
			error_log("could not set in memcache: key -> " . $key . " value: " . var_export($value, true) . "\n", 3, "/var/log/nearpod/cache.log");
		}
	}

	/**
	 * removes a value associated to the provided key
	 *
	 * @param string $key
	 */
	public function delete($key) {
		$this->memcache->delete($this->getActualKey($key));
	}

	/**
	 * Returns a cached element by it's key, or false if not found or error.
	 *
	 * @param string $key The key which it was stored.
	 * @return mixed
	 */
	public function fetch($key, $getActualKey = true) {

		if($getActualKey){
			$key = $this->getActualKey($key);
		}
		$data = $this->memcache->get($key,0);

		if ($data === false) {
			return false;
		}

		return $this->decodeValue($data);
	}

	/**
	 * Increments the key.
	 * @param string $key the key to be incremented
	 * @param int $val optional value to increment, by default is 1
	 * @return bool
	 */
	public function increment($key, $val = 1) {
		return $this->memcache->increment($this->getActualKey($key), $val);
	}

	/**
	 * Asociates the key with the value given.
	 * @param string $key
	 * @param mixed $value
	 * @return bool
	 */
	 public function add($key, $value) {
		$this->memcache->add($this->getActualKey($key), $value,false, $this->getActualExpirationTime());
	 }

	/**
	 * Close the memcache connections.
	 */
	public function close() {
		$this->memcache->close();
	}

	/**
	 * Returns the actual key used for storing an entry.
	 *
	 * @param string $key The external key.
	 * @return string
	 */
	private function getActualKey($key) {

		return CACHE_INITIAL_KEY . '/' . $key;
	}

	/**
	 * Encodes a value for storing it in memcache.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function encodeValue($value) {
		return $value;
	}

	/**
	 * Decodes a value stored in memcache.
	 *
	 * @param string $data
	 * @return mixed
	 */
	private function decodeValue($data) {
		return $data;
	}

	/**
	 * Returns the actual expiration time to store an entry in memcache.
	 *
	 * @return int The unix epoch time.
	 */
	private function getActualExpirationTime() {

		if ($this->lifeTime === 0) {
			return 0;
		}

		return time() + $this->lifeTime;
	}

	function addFromWebService($data, $entity){
		$key = $this->createKeyFromRequest($entity);
		$this->store($key, $data);
	}

	function getCacheKeys(){
		$list = array();
	    $allSlabs = $this->memcache->getExtendedStats('slabs');

	    $items = $this->memcache->getExtendedStats('items');

	    foreach($allSlabs as $server => $slabs) {
    	    foreach($slabs AS $slabId => $slabMeta) {
    	    	if ($slabId >0){
	    	        $cdump = $this->memcache->getExtendedStats('cachedump',(int)$slabId);

	    	        foreach($cdump AS $server => $entries) {
	    	            if($entries) {
	    	            	var_dump($entries);
	        	            foreach($entries AS $eName => $eData) {
	        	            	if (contains($eName, "/")){
	        	            		$items = explode("/", $eName);
	        	            		$eName= $items[1];
	        	            	}
	        	            	array_push($list, $eName);
	        	            }
	    	            }
	    	        }
    	    	}
    	    }
	    }

	    return $list;
	}

	function clearEntityKeys($entity){
		$entity= Cache::$keySeparator.$entity.Cache::$keySeparator;
		$keys = $this->getCacheKeys();
		for ($i = 0; $i < count($keys); $i++) {
			if (contains(strtoupper($keys[$i]), strtoupper($entity))){
				$this->delete($keys[$i]);
			}
		}
	}

	public static function getCacheInstance($lifetime = null){
		if(isset($lifetime) && $lifetime != null){
			$cacheLifetime = $lifetime;
		} else {
			$cacheLifetime = CACHE_LIFETIME;
		}

		if (CACHE_ENABLED) {
			try {
				$cache = new Cache(
				array(
					array(
				      'host' => CACHE_HOST,
				      'port' => CACHE_PORT,
				      'persistent' => CACHE_PERSISTENT,
				      'weight' => CACHE_WEIGHT
				    ),
				 ),
				$cacheLifetime
				);
				return $cache;
			} catch (Exception $exception) {
				return false;
			}
		} else {
			return false;
		}
	}

}

?>
