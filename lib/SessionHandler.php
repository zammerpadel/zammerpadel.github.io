<?php

class NPSessionHandler
{
    private $lifetime = 0;
	private $cache;
	private $sessionsCounter;
	private $noSession = false;

    public function open($savePath, $sessionId) {

    	$this->lifetime = ini_get('session.gc_maxlifetime');
    	$this->cache = Cache::getCacheInstance($this->lifetime);
    	if(session_id() == "" || $this->read(session_id()) === false){
    		if(isset($_SERVER['REMOTE_ADDR'])){
		    	$ip = $_SERVER['REMOTE_ADDR'];
				$stat_key = 'sessionsPerMinute_'.date('YMDHi') . '_'.  $ip;

				if ( ! ($current = $this->cache->increment($stat_key))){
					if (!$this->cache->add($stat_key, 0)){
						$current = $this->cache->increment($stat_key);
					}
				}

				if (!$current || $current > CACHE_SESSIONS_LIMIT) {
					$this->noSession = true;
					return false;
				}
    		}

			$openSessions = 'ct_sessionsPerMinute_'.date('YMDHi');
			if ( ! ($current = $this->cache->increment($openSessions))){
				if (!$this->cache->add($openSessions, 0)){
					$current = $this->cache->increment($openSessions);
				}
			}
    	}

    	return true;
    }

    public function read($id)
    {
    	if($this->noSession){
    		return false;
    	}
        return $this->cache->fetch($id);
    }

    public function write($id, $data)
    {
    	if($this->noSession){
    		return false;
    	}
        return $this->cache->store($id, $data);
    }

    public function destroy($id)
    {
        return $this->cache->delete($id);
    }

    public function gc($lifetime){ return true; }

    public function close(){
    	$this->cache->close();
    	return true;
    }

    public function __construct(){}

    public function __destruct()
    {
        session_write_close();
    }
}
?>