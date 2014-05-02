<?php
/**
 * Utility class to creat UIDs.
 */
class UidGenerator {

	/**
	 * Minimun length of the random string generated for the uid seed.
	 * @var string
	 */
	private $minSeedLength = 32;
	
	/**
	 * Maximun length of the random string generated for the uid seed.
	 * @var string
	 */
	private $maxSeedLength = 64;
	
	/**
	 * Returns a new 32chars UID. It's by no mean unique in any table, the user is
	 * responsible for testing its uniqueness.
	 * 
	 * @return string
	 */
	public function getUid() {
		$randomString = '';
		$randomStringLength = rand($this->minSeedLength, $this->maxSeedLength);
		
		for ($i = 0; $i < $randomStringLength; $i += 1) {
			$randomString .= chr(rand(0, 127));
		}
		
		return md5($randomString . uniqid());
	}
	
	
}