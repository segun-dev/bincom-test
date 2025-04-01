<?php
include 'database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="PU_page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Government Results</title>
</head>

<body>
    <header>
        <h1>Velastic</h1>
    </header>
    <main>
        <section>
            <hr>
            <div class="container">
                <h1>Local Government:</h1>
                <form method="POST" action="">
                    <?php
                    ?>
                    <select name="lga" id="lga" onchange="this.form.submit()">
                        <option value="lga">Select Local Government</option>
                        <!-- to get all local government -->
                        <?php
                        $selected_lga = isset($_POST['lga']) ? $_POST['lga'] : '';

                        // Get all polling units for the dropdown
                        $sql_lga = "SELECT lga_id, lga_name FROM lga";
                        $result_units = $conn->query($sql_lga);
                        if ($result_units->num_rows > 0) {
                            while ($row = $result_units->fetch_assoc()) {
                                $selected = ($selected_lga == $row['lga_id']) ? 'selected' : '';
                                echo "<option value='" . $row['lga_id'] . "' " . $selected . ">" . $row['lga_name'] . "</option>";
                            }
                        }
                        //get PU

                        ?>
                    </select>
                </form>
                <?php
                // Display results if any LGA is selected
                if (!empty($selected_lga)) {
                    // Get LGA name
                    $sql_lga_name = "SELECT lga_name FROM lga WHERE lga_id = '$selected_lga'";
                    $result_name = $conn->query($sql_lga_name);
                    $lga_name = $result_name->fetch_assoc()['lga_name'];

                    echo "<h3>Results for LGA: " . htmlspecialchars($lga_name) . "</h3>";

                    // Get all polling units in this LGA
                    $sql_polling_units = "SELECT uniqueid, polling_unit_name 
                         FROM polling_unit 
                         WHERE lga_id = '$selected_lga'";
                    $result_units = $conn->query($sql_polling_units);

                    if ($result_units->num_rows > 0) {
                        echo "<table class='animate__animated animate__fadeIn'>
                <tr>
                    <th>Polling Unit</th>
                    <th>Total Votes</th>
                </tr>";

                        $lga_total_votes = 0;

                        // Loop through each polling unit
                        while ($unit_row = $result_units->fetch_assoc()) {
                            $polling_unit_id = $unit_row['uniqueid'];
                            $polling_unit_name = $unit_row['polling_unit_name'];

                            // Get total votes for this polling unit
                            $sql_votes = "SELECT SUM(party_score) as total_votes 
                         FROM announced_pu_results 
                         WHERE polling_unit_uniqueid = '$polling_unit_id'";
                            $result_votes = $conn->query($sql_votes);
                            $votes_row = $result_votes->fetch_assoc();
                            $unit_total_votes = $votes_row['total_votes'] ?: 0; // Use 0 if null

                            echo "<tr>
                    <td>" . htmlspecialchars($polling_unit_name) . "</td>
                    <td>" . htmlspecialchars($unit_total_votes) . "</td>
                  </tr>";

                            $lga_total_votes += $unit_total_votes;
                        }

                        echo "<tr>
                <th>Total LGA Votes</th>
                <td><strong>" . $lga_total_votes . "</strong></td>
              </tr>";
                        echo "</table>";
                    } else {
                        echo "<p>No polling units found for this LGA.</p>";
                    }
                }

                $conn->close();
                ?>



        </section>


        <div class="display-table">
                <p><strong>Note:</strong> Select Local Government Area to view results</p>
                <p>Go <a href="index.php"> Home</a></p>
                <p>Check Results for <a href="LGA_page.php"> Individual Polling Unit</a></p>
        </div>
        </main>

    <footer>
        <p>&copy; 2025 Velastic</p>
    </footer>
</body>

</html>