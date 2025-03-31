<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
</head>
<body>
    <form method="post">
        <input type="text" name="user_name" >
        <button type="submit" name= "view"> 
        </form>
        <?php
        if(isset($_POST['user_name']))
        echo $_POST['user_name'];
    ?>
</body>
</html>
<!-- <form method="POST" action="">
                    <input type="text" name="name">
                    <button type="submit" name="view">View Results</button>
                    </form> -->

                    for dropdown retrieve
                    $sql = "SELECT * FROM `polling_unit`  ";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_array($result)){
                        ?>

                        // <option value="<?php echo $row ['uniqueid'];?>"><?php echo $row['polling_unit_name']; ?></option>
                        // <?php  ?>