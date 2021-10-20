<?php include 'form_logic.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form</title>
    <!--suppress SpellCheckingInspection -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
          crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
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
                        <option value="<?php echo $value[0] ?>"
                            <?= (isset($_POST['type']) && $_POST['type'] == $value[0]) ? 'selected' : '' ?>>
                            <?php echo $value[0] ?></option>>
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