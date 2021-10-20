<?php
include 'process_query.php';
// parse configuration file with data for db connection
$config = parse_ini_file('/home/mathew/Documents/work/ktelekom_test_task/ktelekom-test/db.ini');
// connect to a db
$connection = mysqli_connect("localhost", $config['username'], $config['password'], $config['db']);
// check the connection
if (!$connection) {
    echo 'Connection error: ' . mysqli_connect_error(); //only for developing process, remove before production
}
// Filling array to fill the drop-down-list
$query = "SELECT hw_type FROM hw_type";
$hw_types = process_query($connection, $query, 'all');
$hw_types = array_merge(array(0 => array(0 => 'Не выбрано')), $hw_types);
// initialise assoc array for possible error messages
$errors = array(
    'serial_number' => '',
    'hardware_type' => ''
);
$status = ''; //setting the status variable in order to store status of the operation to echo it at the end of form
$status_color = '';
// initialising variables for data received from form
$sn = '';
$type = '';
// check if submit button was pressed
if (isset($_POST['submit'])) {
    // check if type was not selected
    if ($_POST['type'] == 'Не выбрано') {
        $errors['hardware_type'] = 'Пожалуйста, выберите тип оборудования.';
    } else {
        $type = mysqli_real_escape_string($connection, $_POST['type']);
    }
    //make a query to a db to get the regexp of current hw type
    $query = "SELECT sn_mask FROM hw_type WHERE hw_type='$type'";
    $sn_mask = process_query($connection, $query, 'one');
    //check if serial number is empty
    if (empty($_POST['serial_number'])) {
        $errors['serial_number'] = 'Нужно ввести серийный номер.';
    } else {
        $sn = mysqli_real_escape_string($connection, $_POST['serial_number']);
        if (!preg_match($sn_mask, $sn)) { // check for matching the regexp
            $errors['serial_number'] = 'Серийный номер должен соответствовать маске выбранного типа.';
        }
    }
    // make a query to a db to get the id of current type
    $query = "SELECT id FROM hw_type WHERE hw_type='$type'";
    $type_id = process_query($connection, $query, 'one');
    // check if form contains any errors
    if (!array_filter($errors)) {
        $query = "SELECT EXISTS(SELECT * from hw_actual WHERE serial_number='$sn')";
        $is_present = process_query($connection, $query, 'one');
        if (!$is_present) { // check for duplicates
            // generating a query to insert a new record to a db
            $query = "INSERT INTO hw_actual(type_id,serial_number) VALUES ('$type_id', '$sn')";
            if (mysqli_query($connection, $query)) { //sending a query
                $status = 'Запись успешно добавлена.';
                $status_color = 'green-text';
                $_POST['serial_number'] = '';
                $_POST['type'] = 'Не выбрано';
            } else {
                // only for development process, remove before production
                $status = 'Не удалось добавить данные в базу, подробности: ' . mysqli_error($connection);
                $status_color = 'red-text';
            }
        } else {
            $status = 'Такая запись в базе уже существует.';
            $status_color = 'red-text';
        }
    }
    mysqli_close($connection);
}
