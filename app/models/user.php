<?php
require_once 'db.php';
require_once 'session.php';

class Users extends AbstractModel {
    private $name;
    private $username;
    private $password;

    protected static $primaryKey = 'id';

    protected static $tableName = "users";
    public static $tableSchema = array(
        'name' => PDO::PARAM_STR,
        'username' => PDO::PARAM_STR,
        'password' => PDO::PARAM_STR,
    );

    public function __construct($name, $username, $password) {
        global $conn;

        $this->name = $name;
        $this->username = $username;
        $this->password = $password;
    }

    public function __get($prop) {
        return $this->$prop;
    }

    public function __set($prop, $value) {
        $this->$prop = $value;
    }

    public function getTableName() {
        return self::$tableName;
    }

    public function login($username, $password) {
        global $conn, $_sess;

        $sql = 'SELECT * FROM ' . static::$tableName . " WHERE `username` = '" . $username . "' AND `password` = '" . $password . "'";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute() === true) {
            $res = array_shift($stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema)));
            $_sess->id = $res->id;
            $_sess->name = $res->name;
            return true;
        } else {
            return false;
        }
    }

    public function register() {
        if (parent::create()) {
            $this->login($this->username, $this->password);
            return true;
        } else {
            return false;
        }
    }
}

$user = new Users('', '', '');

if (isset($_POST['register'])) {
    $user->name = $_POST['name'];
    $user->username = $_POST['username'];
    $user->password = md5($_POST['password']);

    $user->register();
} elseif (isset($_POST['login'])) {
    $user->login($_POST['username'], md5($_POST['password']));
} elseif (isset($_POST['logout'])) {
    $_sess->kill();
} else {
    return false;
}