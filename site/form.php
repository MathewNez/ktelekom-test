<?php
function process_query($conn, $query, $mode) {
    $result = mysqli_query($conn, $query);
    $retval = [];
    switch ($mode) {
        case 'all':
            $retval = mysqli_fetch_all($result);
            break;
        case 'one':
            $retval = mysqli_fetch_row($result) [0];
            break;
    }
    mysqli_free_result($result);
    return $retval;
}

// parse configuration file with data for db connection
$config = parse_ini_file('/home/mathew/Documents/work/ktelekom_test_task/ktelekom-test/db.ini');
// connect to a db
$connection = mysqli_connect("localhost", $config['username'], $config['password'], $config['db']);
// check the connection
if (!$connection)
{
    echo 'Connection error: ' . mysqli_connect_error(); //only for developing process, remove before production

}
// Declaring array to fill the drop-down-list
$query = "SELECT hw_type FROM hw_type";
$hw_types = process_query($connection, $query, 'all');
$hw_types = array_merge(Array(0 => Array(0 => 'Не выбрано')), $hw_types);
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
if (isset($_POST['submit']))
{

    // check if type was not selected
    if ($_POST['type'] == 'Не выбрано')
    {
        $errors['hardware_type'] = 'Пожалуйста, выберите тип оборудования.';
    }
    else
    {
        $type = mysqli_real_escape_string($connection, $_POST['type']);
    }
    //make a query to a db to get the regexp of current hw type
    $query = "SELECT sn_mask FROM hw_type WHERE hw_type='$type'";
    $sn_mask = process_query($connection, $query, 'all');
    //check if serial number is empty
    if (empty($_POST['serial_number']))
    {
        $errors['serial_number'] = 'Нужно ввести серийный номер.';
    }
    else
    {
        $sn = mysqli_real_escape_string($connection, $_POST['serial_number']);
        if (!preg_match($sn_mask, $sn))
        { // check for matching the regexp
            $errors['serial_number'] = 'Серийный номер должен соответствовать маске выбранного типа.';
        }
    }
    // make a query to a db to get the id of current type
    $query = "SELECT id FROM hw_type WHERE hw_type='$type'";
    $type_id = process_query($connection, $query, 'one');
    // check if form contains any errors
    if (!array_filter($errors))
    {
        $query = "SELECT EXISTS(SELECT * from hw_actual WHERE serial_number='$sn')";
        $is_present = process_query($connection, $query, 'one');
        if (!$is_present)
        { // check for dublicates
            // generating a query to insert a new record to a db
            $query = "INSERT INTO hw_actual(type_id,serial_number) VALUES ('$type_id', '$sn')";
            if (mysqli_query($connection, $query))
            { //sending a query
                $status = 'Запись успешно добавлена.';
                $status_color = 'green-text';
                $_POST['serial_number'] = '';
                $_POST['type'] = 'Не выбрано';

            }
            else
            {
                $status = 'Не удалось добавить данные в базу, подробности: ' . mysqli_error($connection); // only for development process, remove before production
                $status_color = 'red-text';

            }
        }
        else
        {
            $status = 'Такая запись в базе уже существует.';
            $status_color = 'red-text';

        }

    }
    mysqli_close($connection);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        .red-text {
            color: red;
        }

        .green-text {
            color: green;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<main class="form-signin">
    <form action="form.php" method="post" class="align-items-center">
        <div class="mb-3">
            <label class="form-label">Серийный номер:
                <input type="text" name="serial_number" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['serial_number']); ?>"/>
            </label>
            <div class="red-text"><?php echo $errors['serial_number']; ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label">Тип оборудования:
                <select name="type" class="form-select">
                    <?php foreach ($hw_types as $value) { ?>
                        <option value="<?php echo $value[0] ?>" <?= (isset($_POST['type']) && $_POST['type'] == $value[0]) ? 'selected' : '' ?>><?php echo $value[0] ?></option>>
                    <?php } ?>
                </select>
            </label>
            <div class="red-text"><?php echo $errors['hardware_type']; ?></div>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Add</button>
        <div class="<?php echo $status_color ?>"><?php echo $status; ?></div>
    </form>
    <main>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
                integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
                crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
                integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
                crossorigin="anonymous"></script>
</body>
</html>