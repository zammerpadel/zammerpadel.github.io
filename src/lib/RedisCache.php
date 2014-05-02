<?php

class RedisCache {

	/**
	* The Redis server
	* @var Redis
	*/
	private $redis;

	private static $instance = null;


	/**
	 * Creates a new cache manager.
	 *
	 * @param array $serversData An array of servers config.
	 */
	private function __construct($config) {
		$this->redis = new Redis();
		$this->redis->connect($config['host'], $config['port']);
	}

	/**
	 * Stores a value associated to the provided key
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public static function setExpire($key, $value, $lifetime = REDIS_LIFETIME) {
		$redis = self::getInstance();
		return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
	}

	/**
	* Stores a value associated to the provided key
	*
	* @param string $key
	* @param mixed $value
	*/
	public static function set($key, $value) {
		$redis = self::getInstance();
		return $redis->redis->set($key, $redis->serialize($value));
	}

	/**
	 * removes a value associated to the provided key
	 *
	 * @param string $key
	 */
	public static function delete($key) {
		return self::getInstance()->redis->delete($key);
	}

	/**
	 * Returns a cached element by it's key, or false if not found or error.
	 *
	 * @param string $key The key which it was stored.
	 * @return mixed
	 */
	public static function get($key) {
		$redis = self::getInstance();
		return $redis->unserialize($redis->redis->get($key));
	}

	/**
	 * Close the redis connections.
	 */
	public static function close() {
		return self::getInstance()->redis->close();
	}

	private function serialize($value){
		return json_encode($value);
	}

	private function unserialize($value){
		return json_decode($value,true);
	}

	/**
	*
	* @return RedisCache
	*/
	public static function getInstance(){
		if(self::$instance != null){
			return self::$instance;
		}
		if (REDIS_ENABLED) {
			self::$instance = new RedisCache(
				array(
			      'host' => REDIS_HOST,
			      'port' => REDIS_PORT,
			    )
			);

			return self::$instance;
		} else {
			return false;
		}
	}

	//PRESENTATION SESSION
	private function getPresentationSessionKey($presentation_uid){
		return "presentation_session_" . $presentation_uid;
	}

	public static function setExpirePresentationSession($presentation_uid, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPresentationSessionKey($presentation_uid);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setPresentationSession($presentation_uid, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPresentationSessionKey($presentation_uid);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getPresentationSession($presentation_uid){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPresentationSessionKey($presentation_uid);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}


	//TEACHER SESSION
	private function getTeacherSessionKey($teacher_id){
		return "teacher_session_" . $teacher_id;
	}

	public static function setExpireTeacherSession($teacher_id, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getTeacherSessionKey($teacher_id);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setTeacherSession($teacher_id, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getTeacherSessionKey($teacher_id);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getTeacherSession($teacher_id){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getTeacherSessionKey($teacher_id);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}

	//TEACHER BY DEVICE
	private function getTeacherByDeviceKey($device_uid){
		return "teacher_" . $device_uid;
	}

	public static function setExpireTeacherByDevice($device_uid, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getTeacherByDeviceKey($device_uid);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setTeacherByDevice($device_uid, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getTeacherByDeviceKey($device_uid);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getTeacherByDevice($device_uid){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getTeacherByDeviceKey($device_uid);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}

	//PRESENTATION HOMEWORK
	private function getPresentationHomeworkKey($presentation_uid){
		return "presentation_homework_" . $presentation_uid;
	}

	public static function setExpirePresentationHomework($session_uid, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPresentationHomeworkKey($session_uid);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setPresentationHomework($session_uid, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPresentationHomeworkKey($session_uid);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getPresentationHomework($presentation_uid){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPresentationHomeworkKey($presentation_uid);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}

	//PIN SESSION
	private function getPinSessionKey($pin){
		return "pin_session_" . $pin;
	}

	public static function setExpirePinSession($pin, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPinSessionKey($pin);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setPinSession($pin, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPinSessionKey($pin);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getPinSession($pin){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getPinSessionKey($pin);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}

	//SESSION
	private function getSessionKey($session_uid){
		return "session_" . $session_uid;
	}

	public static function setExpireSession($session_uid, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getSessionKey($session_uid);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setSession($session_uid, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getSessionKey($session_uid);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getSession($session_uid){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getSessionKey($session_uid);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}

	//PARTICIPANT BY DEVICE
	private function getParticipantByDeviceKey($device_uid){
		return "participant_" . $device_uid;
	}

	public static function setExpireParticipantByDevice($device_uid, $value, $lifetime = REDIS_LIFETIME){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getParticipantByDeviceKey($device_uid);
			return $redis->redis->setex($key, $lifetime, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function setParticipantByDevice($device_uid, $value){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getParticipantByDeviceKey($device_uid);
			return $redis->redis->set($key, $redis->serialize($value));
		}else{
			return false;
		}
	}

	public static function getParticipantByDevice($device_uid){
		$redis = self::getInstance();
		if($redis){
			$key = $redis->getParticipantByDeviceKey($device_uid);
			return $redis->unserialize($redis->redis->get($key));
		}else{
			return false;
		}
	}

}