$(document).ready(function () {
    /* Main */
    $("#add-task").hide();
    $('dialog').hide();

});

/* Header */
$('#add-new').on('click', function () {
    $("#add-task").toggle('slow');
});

/* Login & Signup Dialog */
$("#login").on('click', function () {
    $('dialog.signup').hide();
    $('dialog.login').show();
});
$("#signup").on('click', function () {
    $('dialog.login').hide();
    $('dialog.signup').show();
});
$(".close").on('click', function () {
    $('dialog').hide();
    return false;
});

/* Login Action */
$("#dologin").click(function () {
    var username = $("#username").val(),
        password = $("#password").val();
    $.ajax({
        type: "POST",
        url: "app/models/user.php",
        data: "username=" + username + "&password=" + password + "&login",
        success: function (html) {
            window.location.reload();
        }
    });
});
/* Signup Action */
$("#dosignup").click(function () {
    var s_username = $("#s_username").val(),
        s_password = $("#s_password").val(),
        s_name = $("#s_name").val();
    $.ajax({
        type: "POST",
        url: "app/models/user.php",
        data: "name=" + s_name + "&username=" + s_username + "&password=" + s_password + "&register",
        success: function (html) {
            window.location.reload();
        }
    });
});
/* Logout Action */
$("#logout").click(function () {
    $.ajax({
        type: "POST",
        url: "app/models/user.php",
        data: "logout",
        success: function (html) {
            window.location.reload();
        }
    });
});

/* Tasks */
/* Add Tasks */
$("#addtask").click(function () {
    var task = $("#taskname").val(),
        notes = $("#notes").val(),
        datetime = $('input[name=datetime]').val().replace('T', ' ');
    $.ajax({
        type: "POST",
        url: "app/models/task.php",
        data: "task=" + task + "&notes=" + notes + "&datetime=" + datetime + "&add",
        success: function (html) {
            $("#add-task").toggle('slow');
            window.location.reload();
        },
        error: function () {
            alert('Please, Try Again!');
            return false;
        }
    });
    return false;
});
$('#close-a').on('click', function () {
    $("#add-task").toggle('slow');
    return false;
});

/* Change Task State */
$("input[type=checkbox]").on('change', function () {
    this.value = (Number(this.checked));
    $.ajax({
        type: "POST",
        url: "app/models/task.php",
        data: "state=" + this.value + "&id=" + (parseInt(this.name)) + "&update"
    });
});
/* Delete Task */
$(".delete").click(function () {
    var task_id = this.name;
    $.ajax({
        type: "POST",
        url: "app/models/task.php",
        data: "id=" + (parseInt(task_id)) + "&delete",
        success: function (html) {
            window.location.reload();
        },
        error: function () {
            alert('Please, Try Again!');
            return false;
        }
    });
});