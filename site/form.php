<?php
    // connect to a db
    $connection = mysqli_connect(localhost, '', '', 'hardware'); //TODO make the password translation more secure
    // check the connection
    if(!$connection) {
        echo 'Connection error: ' . mysqli_connect_error(); //only for developing process
    }

    if(isset($_POST['submit'])) {
        echo $_POST['serial_number'];
        echo $_POST['type'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form</title>
</head>
<body>
<form action="form.php" method="post">
    <p>Серийный номер: <input type="text" name="serial_number" /></p>
    <p>Тип оборудования: <select name="type" id="types">
        <option value="TP-Link TL-WR74">TP-Link TL-WR74</option>
        <option value="D-Link DIR-300">D-Link DIR-300</option>
        <option value="D-Link DIR-300 S">D-Link DIR-300 S</option>
    </select> </p>
    <p><input type="submit" name="submit" value="Добавить"/></p>
</form>
</body>
</html>