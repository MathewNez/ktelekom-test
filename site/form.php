<?php
    // parse configuration file with data for db connection
    $config = parse_ini_file('/home/mathew/Documents/work/ktelekom_test_task/ktelekom-test/db.ini');
    // connect to a db
    $connection = mysqli_connect("localhost", $config['username'], $config['password'], $config['db']);
    // check the connection
    if(!$connection) {
        echo 'Connection error: ' . mysqli_connect_error(); //only for developing process, remove before production
    }
    // Declaring array to fill the drop-down-list
    $hw_types = ['Не выбрано', 'TP-Link TL-WR74', 'D-Link DIR-300', 'D-Link DIR-300 S'];
    // initialise assoc array for possible error messages
    $errors = array('serial_number'=>'', 'hardware_type'=>'');
    $status = '';   //setting the status variable in order to store status of the operation to echo it at the end of form
    // initialising variables for data received from form
    $sn = '';
    $type = '';
    // check if submit button was pressed
    if(isset($_POST['submit'])) {


        // check if type was not selected
        if($_POST['type'] == 'Не выбрано') {
            $errors['hardware_type'] = 'You should select the hardware type';
        } else {
            $type = mysqli_real_escape_string($connection, $_POST['type']);
        }
        //make a query to a db to get the regexp of current hw type
        $query = "SELECT sn_mask FROM hw_type WHERE hw_type='$type'";
        $result = mysqli_query($connection, $query);
        $sn_mask = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['sn_mask'];
        mysqli_free_result($result);
        //check if serial number is empty
        if (empty($_POST['serial_number'])) {
            $errors['serial_number'] = 'A serial number is required';
        } else {
            $sn = mysqli_real_escape_string($connection, $_POST['serial_number']);
            if(!preg_match($sn_mask, $sn)) {    // check for matching the regexp
                $errors['serial_number'] = 'Serial number must match the current hardware type mask!';
            }
        }
        // make a query to a db to get the id of current type
        //TODO make this block as a function in order not to repeat myself
        $query = "SELECT id FROM hw_type WHERE hw_type='$type'";
        $result = mysqli_query($connection, $query);
        $type_id = mysqli_fetch_all($result, MYSQLI_ASSOC)[0]['id'];
        mysqli_free_result($result);
        // check if form contains any errors
        if(array_filter($errors)) {
//            echo 'Form contains errors';
        } else {
            $query = "SELECT EXISTS(SELECT * from hw_actual WHERE serial_number='$sn')";
            $result = mysqli_query($connection, $query);
            $is_present = mysqli_fetch_row($result)[0];
            mysqli_free_result($result);
            if (!$is_present){  // check for dublicates
                // generating a query to insert a new record to a db
                $query = "INSERT INTO hw_actual(type_id,serial_number) VALUES ('$type_id', '$sn')";
                if(mysqli_query($connection, $query)) { //sending a query
                    $status = 'Item was successfully added to a database.';
//                  header('Location: form.php');
                } else {
                    $status = 'Error adding values to the database: ' . mysqli_error($connection); // only for development process, remove before production
                }
            } else {
                $status = 'There is already one unit in database with this serial number';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<style >
    .red-text {
        color:red;
    }
</style>

<body>
<div class="d-flex justify-content-center align-self-center">
    <form action="form.php" method="post">
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
                        <option value="<?php echo $value ?>" <?= (isset($_POST['type']) && $_POST['type'] == $value) ? 'selected' : '' ?>><?php echo $value ?></option>>
                    <?php } ?>
                </select>
            </label>
            <div class="red-text"><?php echo $errors['hardware_type']; ?></div>
        </div>

        <button type="submit" name= "submit" class="btn btn-primary">Add</button>
        <div class="red-text"><?php echo $status; ?></div>
    </form>
</div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</body>
</html>