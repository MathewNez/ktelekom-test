<?php
    $config = parse_ini_file('/home/mathew/Documents/work/ktelekom_test_task/ktelekom-test/db.ini');
    // connect to a db
    $connection = mysqli_connect("localhost", $config['username'], $config['password'], $config['db']);
    // check the connection
    if(!$connection) {
        echo 'Connection error: ' . mysqli_connect_error(); //only for developing process, remove before production
    }

    if(isset($_POST['submit'])) {
        $sn = '';
        $type = '';

        // regexp for tplink /^[0-9A-Z]{2}[A-Z]{5}[0-9A-Z][A-Z]{2}$/
        // regexp for dlink /^[0-9][0-9A-Z]{2}[A-Z]{2}[0-9A-Z][-_@][0-9A-Z][a-z]{2}$/
        // regexp for dlink s /^[0-9][0-9A-Z]{2}[A-Z]{2}[0-9A-Z][-_@][0-9A-Z]{3}$/

        if($_POST['type'] == 'Не выбрано') {
            echo 'You should select the hw type';
        } else {
            $type = mysqli_real_escape_string($connection, $_POST['type']);
        }

        $query = "SELECT sn_mask FROM hw_type WHERE hw_type='$type'";
        $result = mysqli_query($connection, $query);
        $sn_mask = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['sn_mask'];
        mysqli_free_result($result);

        if (empty($_POST['serial_number'])) {
            echo 'A serial number is required <br />';
        } else {
            $sn = mysqli_real_escape_string($connection, $_POST['serial_number']);
            if(!preg_match($sn_mask, $sn)) {
                echo 'Serial number must match the current hardware type mask! <br />';
            }
        }

        $query = "SELECT id FROM hw_type WHERE hw_type='$type'";
        $result = mysqli_query($connection, $query);
        $type_id = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['id'];
        mysqli_free_result($result);

        $query = "INSERT INTO hw_actual(type_id,serial_number) VALUES ('$type_id', '$sn')";
        if(mysqli_query($connection, $query)) {
            echo 'Item was successfully added to a database.';
        } else {
            echo 'Error adding values to the database: ' . mysqli_error($connection); // only for development process, remove before production
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
            <option selected="selected">Не выбрано</option>
        <option value="TP-Link TL-WR74">TP-Link TL-WR74</option>
        <option value="D-Link DIR-300">D-Link DIR-300</option>
        <option value="D-Link DIR-300 S">D-Link DIR-300 S</option>
    </select> </p>
    <p><input type="submit" name="submit" value="Добавить"/></p>
</form>
</body>
</html>