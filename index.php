<?php
define('DS', DIRECTORY_SEPARATOR);
require_once 'app' . DS . 'models' . DS . 'task.php';
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php require_once 'app' . DS . 'views' . DS . 'header.php'; ?>
    <title>TODOIT - To Do List App</title>
</head>
<body>
<div class="container">
    <dialog class="login" open>
        <form method="post">
            <input type="text" name="username" placeholder="Username" id="username">
            <input type="password" name="password" placeholder="Password" id="password">
            <div style="margin: 0 auto">
                <input type="submit" value="Login" id="dologin">
                <button type="cancel" class="close">Close</button>
            </div>
        </form>
    </dialog>
    <dialog class="signup" open>
        <form method="post">
            <input type="text" name="name" placeholder="Your Name" id="s_name">
            <input type="text" name="username" placeholder="Username" id="s_username">
            <input type="password" name="password" placeholder="Password" id="s_password">
            <div>
                <input type="submit" value="Signup" id="dosignup">
                <button type="cancel" class="close">Close</button>
            </div>
        </form>
    </dialog>
    <!-- Header -->
    <header>
        <h1>Hi, <span>
                <?php
                if (isset($_sess->name)) {
                    echo $_sess->name;
                } else {
                    echo "Guest";
                }
                ?>
            </span></h1>
        <?php
        if (isset($_sess->name)) {
            echo '<button class="fa fa-plus" id="add-new"></button>';
            echo '<button id="logout">Logout</button>';
        } else {
            echo '<button id="login">Login</button>
                    <button id="signup">Signup</button>';
        }
        ?>
    </header>
    <!-- Tasks -->
    <section class="tasks">
        <form method="post" id="add-task">
            <input type="text" name="task" id="taskname" placeholder="Task Name">
            <button type="cancel" class="fa fa-close" id="close-a"></button>
            <input type="submit" class="fa fa-plus" value=&#xf067; id="addtask">
            <div class="clearfix"></div>
            <textarea name="notes" id="notes" placeholder="Notes"></textarea>
            <label>Date-Time: </label><input type="datetime-local" name="datetime">
        </form>
        <div class="tasks-area">
            <?php
            $task = new Tasks('', '', '', '', '');
            if (isset($_sess->name) && isset($_sess->id)) {
                $task->getAll();
            } else {
                echo '';
            }
            ?>
        </div>
    </section>
</div>
<?php require_once 'app' . DS . 'views' . DS . 'footer.php'; ?>
</body>
</html>