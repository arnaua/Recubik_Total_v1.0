<?php
	
	error_reporting(-1);
	// These constants may be changed without breaking existing hashes.
    const PBKDF2_HASH_ALGORITHM = 'sha256';
    const PBKDF2_ITERATIONS = 1000;
    const PBKDF2_SALT_BYTES = 24;
    const PBKDF2_HASH_BYTES = 24;

    // These constants define the encoding and may not be changed.
    const HASH_SECTIONS = 4;
    const HASH_ALGORITHM_INDEX = 0;
    const HASH_ITERATION_INDEX = 1;
    const HASH_SALT_INDEX = 2;
    const HASH_PBKDF2_INDEX = 3;

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword($password, $hash)
    {
        $params = explode(':', $hash);
        if (count($params) < self::HASH_SECTIONS)
            throw new \InvalidArgumentException('Invalid password hash - not enough hash sections');

        $pbkdf2 = base64_decode($params[self::HASH_PBKDF2_INDEX]);

        // Check to see if the hash created from the provided password is the same as the hash we have stored already
        return (self::slowEquals(
            $pbkdf2,
            self::pbkdf2(
                $params[self::HASH_ALGORITHM_INDEX],
                $password,
                $params[self::HASH_SALT_INDEX],
                (int)$params[self::HASH_ITERATION_INDEX],
                strlen($pbkdf2),
                true
            )
        ));
    }

    /**
     * Compares two strings $a and $b in length-constant time.
     * @param string $a
     * @param string $b
     * @return bool
     */
    private static function slowEquals($a, $b)
    {
        $diff = strlen($a) ^ strlen($b);
        for($i = 0; $i < strlen($a) && $i < strlen($b); $i++)
        {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0;
    }

    /**
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     *
     * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
     *
     * This implementation of PBKDF2 was originally created by https://defuse.ca
     * With improvements by http://www.variations-of-shadow.com
     *
     * @param string $algorithm The hash algorithm to use. Recommended: SHA256
     * @param string $password The password.
     * @param string $salt A salt that is unique to the password.
     * @param int $count Iteration count. Higher is better, but slower. Recommended: At least 1000.
     * @param int $key_length The length of the derived key in bytes.
     * @param bool $raw_output If true, the key is returned in raw binary format. Hex encoded otherwise.
     * @return string A $key_length-byte key derived from the password and salt.
     */
    public static function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
    {
        $algorithm = strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true))
            throw new \InvalidArgumentException('PBKDF2 ERROR: Invalid hash algorithm.');
        if ($count <= 0 || $key_length <= 0)
            throw new \InvalidArgumentException('PBKDF2 ERROR: Invalid parameters.');

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for ($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if ($raw_output)
            return substr($output, 0, $key_length);
        else
            return bin2hex(substr($output, 0, $key_length));
    }




	
	$username = 'admin';
	$password = "gestorreverter1994";
	$iterations = 1000;

	$result = verifyPassword($password, '$2y$10$6b.gZ.ld9bPYXieHRZtg5eWdXRdlJ22V.jFwKztJugY467XOl2sUO');

	// Generate a random IV using openssl_random_pseudo_bytes()
	// random_bytes() or another suitable source of randomness
	//$salt = openssl_random_pseudo_bytes(24);

	//$hash = hash_pbkdf2("sha256", $password, $salt, $iterations, 24);
	//echo $hash;
	//echo "</br>";
	//$hash = password_hash("gestorreverter1994", PASSWORD_DEFAULT);
	//$params = explode(':', $hash);
	//$pbkdf2 = base64_decode($params[self::HASH_PBKDF2_INDEX]);
	//echo $hash;
	//echo "</br>";
	//print_r($params);
	echo $result;
	echo "</br>";

	




	$password = md5('gestorreverter1994');

	$servername = "localhost";
    $username = "gestordb_user";
    $password = "Bns&6q93";

try {
    $conn = new PDO("mysql:host=$servername;dbname=gestordb", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"; 
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }


    $sql = "SELECT UserID,  FROM usuarios WHERE UserName='".$username."' AND UserPassword='".$password."'";
	//echo "SELECT UserID,  FROM usuarios WHERE UserName='".$username."' AND UserPassword='".$password."'";
    $res = $conn->query($sql)->fetch();
    $firstName = $res['firstName'];

     echo $firstName;


?>