<?php
require_once 'db.php';
require_once 'session.php';

class Tasks extends AbstractModel {
    private $user_id;
    private $title;
    private $description;
    private $datetime;
    private $state;

    protected static $primaryKey = 'id';

    protected static $tableName = "tasks";
    public static $tableSchema = array(
        'user_id' => PDO::PARAM_INT,
        'title' => PDO::PARAM_STR,
        'description' => PDO::PARAM_STR,
        'datetime' => PDO::PARAM_STR,
        'state' => PDO::PARAM_INT
    );

    public function __construct($user_id, $title, $description, $datetime, $state) {
        global $conn;

        $this->user_id = $user_id;
        $this->title = $title;
        $this->description = $description;
        $this->datetime = $datetime;
        $this->state = $state;
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

    public function getAll() {
        global $_sess;

        $this->allTasks = parent::getAll();
        foreach ($this->allTasks as $this->obj) {
            if ($this->obj->user_id == $_sess->id) {
                $state = ($this->obj->state == 1) ? 'checked' : false;
                echo "<div class='task-data'>
                <h1>{$this->obj->title}</h1>
                <p style='width: 100%'>Notes: <span>{$this->obj->description}</span></p>
                <p style='width: 50%'>Date/Time: <span>{$this->obj->datetime}</span></p>
                <div id='task-control'>
                    <label class='switch'>
                        <input type='checkbox' name={$this->obj->id} $state>
                        <div class='slider round'></div>
                    </label>
                    <button class='fa fa-trash delete' name={$this->obj->id}></button>
                </div>
            </div>";
            }
        }
    }
}

$task = new Tasks('', '', '', '', '');

if (isset($_POST['add'])) {
    $task->user_id = $_sess->id;
    $task->title = $_POST['task'];
    $task->description = $_POST['notes'];
    $task->datetime = $_POST['datetime'];
    $task->state = 0;

    $task->create();
} elseif (isset($_POST['delete'])) {
    $task->getByID($_POST['id'])->delete();
} elseif (isset($_POST['update'])) {
    $t = $task->getByID($_POST['id']);
    $t->state = $_POST['state'];
    $t->update();
} else {
    return false;
}