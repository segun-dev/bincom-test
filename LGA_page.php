<?php
include 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="PU_page.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Government Results</title>
</head>
<body>
<header>
    <h1>Velastic</h1>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Polling Unit Results</a></li>
                <li><a href="#">Overall Results</a></li>
                
            </ul>
        </nav>
        <div class="menu">
        <span class="material-symbols-outlined">
menu
</span>
        </div>
    </header>
    <main>
        <section>
            <hr>
            <div class="container">
                <p>Local Government</p>
                <select name="polling_unit" id="polling_unit">
                    <option value="polling_unit">Select Local Government</option>
                    <?php
                        $sql = "SELECT * FROM `polling_unit`  ";
                        $result = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_array($result)){
                        ?>
                        <option value="<?php echo $row ['polling_unit_id'];?>"><?php echo $row['polling_unit_name']; ?></option>
                        <?php } ?>
                    <option value="polling_unit">Polling Unit 1</option>
                    <option value="polling_unit">Polling Unit 5</option>
                    </div>
        </select>
        </section>
        
        <section>
            <div class="display-table">
                <p><strong>Note:</strong> Select Local Government Area to view results</p>
                <table>
  <tr>
    <th>Party</th>
    <th>Votes</th>
  </tr>
  <tr>
    <td>PDP</td>
    <td>500</td>
  </tr>
  <tr>
    <td>APC</td>
    <td>400</td>
  </tr>
  <tr>
    <td>LP</td>
   
    <td>300</td>
  </tr>
</table>
            </div>
        </section>
    </main>
    <footer>
    <p>&copy; 2025 Velastic</p>
</footer>
</body>
</html>