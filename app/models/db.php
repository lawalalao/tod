<?php
$dsn = "mysql://hostname=localhost;dbname=todo"; // Data Source Name
$user = "demo";
$pass = "demo!";
$options = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
);

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo $e->getMessage();
}

class AbstractModel {
    protected function prepareValues(PDOStatement &$stmt) {
        foreach (static::$tableSchema as $col => $type) {
            $stmt->bindParam(":{$col}", $this->$col, $type);
        }
    }

    protected function buildSQLParameters() {
        $namedParams = '';
        foreach (static::$tableSchema as $col => $type) {
            $namedParams .= $col . ' = :' . $col . ', ';
        }
        return trim($namedParams, ', ');
    }

    public function create() {
        global $conn;
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::buildSQLParameters();
        $stmt = $conn->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    public function update() {
        global $conn;
        $sql = 'UPDATE ' . static::$tableName . ' SET ' . self::buildSQLParameters() . ' WHERE ' . static::$primaryKey . ' = ' . $this->{static::$primaryKey};
        $stmt = $conn->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    public function delete() {
        global $conn;
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = ' . $this->{static::$primaryKey};
        $stmt = $conn->prepare($sql);
        return $stmt->execute();
    }

    public function getAll() {
        global $conn;
        $sql = 'SELECT * FROM ' . static::$tableName;
        $stmt = $conn->prepare($sql);
        return $stmt->execute() === true ? $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema)) : false;
    }

    public function getByID($id) {
        global $conn;
        $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = ' . $id;
        $stmt = $conn->prepare($sql);
        if ($stmt->execute() === true) {
            $obj = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, get_called_class(), array_keys(static::$tableSchema));
            return array_shift($obj);
        }
        return false;
    }
}