<?php

class Security
{
    private static $seed = '3575631902';

    public static function encode($data)
    {
        return hash('sha256', Security::$seed . $data);
    }

    static function generateRandomHex() {
		// Generate a 32 digits hexadecimal number
		$numbytes = 16; // Because 32 digits hexadecimal = 16 bytes
		$bytes = openssl_random_pseudo_bytes($numbytes); 
		$hex   = bin2hex($bytes);
		return $hex;
	}
}
