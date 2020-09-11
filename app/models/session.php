<?php

// Session Handler Class
class MySessionHandler extends SessionHandler {
    // Default Session Data
    private $sessionName = "MYSESSID";
    private $sessionLifeTime = 0;
    private $sessionSSL = false;
    private $sessionHTTPOnly = true;
    private $sessionPath = "/";
    private $sessionDomain = ''; // Website Domain
    private $sessionSavePath;

    // Default MCRYPT Extension Parameters
    private $sessionCipherAlgo = MCRYPT_BLOWFISH;
    private $sessionCipherMode = MCRYPT_MODE_ECB;
    private $sessionCipherKey = "WYCRYPT0K3Y0H3LL";

    // Session Life Time in Minutes
    private $ttl = 1;

    public function __construct() {
        // Initialize Sessions' Path
        $this->sessionSavePath = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . 'sessions';

        // Initialize PHP.ini Settings For Session
        ini_set("session.use_cookies", 1);
        ini_set("session.use_only_cookies", 1);
        ini_set("session.use_trans_sid", 0);
        ini_set("session.save_handler", 'files');

        // Session Settings
        session_name($this->sessionName);
        session_save_path($this->sessionSavePath);
        // Set Session Cookie Data
        session_set_cookie_params(
            $this->sessionLifeTime, $this->sessionPath,
            $this->sessionDomain, $this->sessionSSL,
            $this->sessionHTTPOnly
        );
        // Make $this (Object) as a Session Handler
        session_set_save_handler($this, true);
    }

    public function __get($key) {
        return false !== $_SESSION[$key] ? $_SESSION[$key] : false;
    }

    public function __set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function __isset($key) {
        return @$_SESSION[$key] ? true : false;
    }

    public function read($id) {
        // Decrypt Session Data (need to mcrypt ext in PHP 7+)
        $decData = mcrypt_decrypt($this->sessionCipherAlgo, $this->sessionCipherKey, parent::read($id), $this->sessionCipherMode);
        return $decData;
    }

    public function write($id, $data) {
        // Encrypt Session Data (need to mcrypt ext in PHP 7+)
        $encData = mcrypt_encrypt($this->sessionCipherAlgo, $this->sessionCipherKey, $data, $this->sessionCipherMode);
        return parent::write($id, $encData);
    }

    public function start() {
        /*
         * If not Session Already Started
         * Start session & Set StartTime
        */
        if ('' == session_id()) {
            if (session_start()) {
                $this->setSessionStartTime();
                $this->checkSessionValidity();
            }
        }
    }

    public function setSessionStartTime() {
        // Set Session Start Time = Current Time.
        if (!isset($this->sessionStartTime)) {
            $this->sessionStartTime = time();
        }
        return true;
    }

    public function checkSessionValidity() {
        // Compare Current Time with Session Start Time
        // and Regenerate a New ID if Session Life Time Done
        if ((time() - $this->sessionStartTime) > ($this->ttl * 60)) {
            $this->renewSession();
            $this->generateFingerPrint();
        }
        return true;
    }

    public function renewSession() {
        // Regenerate a New Session ID
        $this->sessionStartTime = time();
        return session_regenerate_id(true);
    }

    public function kill() {
        // Kill Session And Clear Cookies
        session_unset();
        setcookie(
            $this->sessionName, '', time() - 1000,
            $this->sessionPath, $this->sessionDomain,
            $this->sessionSSL, $this->sessionHTTPOnly
        );
        session_destroy();
    }

    public function generateFingerPrint() {
        // Make a Finger Print for Every Session
        // to Prevent Session Hijacking Attack (doesn't working with PHP 7+)
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $sessionId = session_id();
        $this->cipherKey = mcrypt_create_iv(32);
        $this->fingerPrint = sha1($userAgent . $this->cipherKey . $sessionId);
    }

    public function isValidFingerPrint() {
        // Check Finger Print
        if (!isset($this->fingerPrint)) {
            $this->generateFingerPrint();
        }
        $fingerPrint = sha1($_SERVER['HTTP_USER_AGENT'] . $this->cipherKey . session_id());
        if ($fingerPrint === $this->fingerPrint) {
            return true;
        }
        return false;
    }
}

// Session Start & Control
if ('' === session_id()) {
    $_sess = new MySessionHandler();
    $_sess->start();
} else if (!$_sess->isValidFingerPrint()) {
    $_sess->kill();
} else {
    return false;
}
?>

