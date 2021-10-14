<?php
    $config = parse_ini_file('db.ini');
    // connect to a db
    $connection = mysqli_connect("localhost", $config['username'], $config['password'], $config['db']); //TODO make the password translation more secure
    // check the connection
    if(!$connection) {
        echo 'Connection error: ' . mysqli_connect_error(); //only for developing process, remove before production
    }

    if(isset($_POST['submit'])) {
        $sn = mysqli_real_escape_string($connection, $_POST['serial_number']);
        $type = mysqli_real_escape_string($connection, $_POST['type']);
        $query = "SELECT id FROM hw_type WHERE hw_type='$type'";
        $result = mysqli_query($connection, $query);
        $type_id = mysqli_fetch_all($result, MYSQLI_ASSOC)[0][id];
        mysqli_free_result($result);
        $query = "INSERT INTO hw_actual(type_id,serial_number) VALUES ('$type_id', '$sn')";
        if(mysqli_query($connection, $query)) {
            echo 'Item was successfully added to a database.';
        } else {
            echo 'Error adding values to the database: ' . mysqli_error(); // only for development process, remove before production
        }
        mysqli_close($connection);
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