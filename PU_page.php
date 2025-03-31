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
    <title>Polling Unit</title>
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

                <form method="post" action="">
                    <label for="polling_unit">Select Polling Unit:</label>
                    <select name="polling_unit" id="polling_unit" onchange="this.form.submit()">
                        <option value="">-- Select Polling Unit --</option>

                        <?php
                        $selected_unit = isset($_POST['polling_unit']) ? $_POST['polling_unit'] : '';

                        // Get all polling units for the dropdown
                        $sql_polling_units = "SELECT uniqueid, polling_unit_number, polling_unit_name FROM polling_unit";
                        $result_units = $conn->query($sql_polling_units);
                        if ($result_units->num_rows > 0) {
                            while ($row = $result_units->fetch_assoc()) {
                                $selected = ($selected_unit == $row['uniqueid']) ? 'selected' : '';
                                echo "<option value='" . $row['uniqueid'] . "' " . $selected . ">" . $row['polling_unit_name'] . " - " . $row['polling_unit_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                </form>

                <?php
                // Display results if a polling unit is selected
                if (!empty($selected_unit)) {
                    
                    // Get polling unit name
                    $sql_unit_name = "SELECT polling_unit_name FROM polling_unit WHERE uniqueid = '$selected_unit'";
                    $result_name = $conn->query($sql_unit_name);
                    $unit_name = $result_name->fetch_assoc()['polling_unit_name'];

                    echo "<h3>Results for: " . htmlspecialchars($unit_name) . "</h3>";

                    // Get results for the selected polling unit
                    $sql_results = "SELECT party_abbreviation, party_score FROM announced_pu_results 
                                            WHERE polling_unit_uniqueid = '$selected_unit' 
                                            ORDER BY party_abbreviation";
                    $result_scores = $conn->query($sql_results);

                    if ($result_scores->num_rows > 0) {
                        echo "<table>
                                        <tr>
                                            <th>Party</th>
                                            <th>Votes</th>
                                        </tr>";

                        $total_votes = 0;
                        while ($row = $result_scores->fetch_assoc()) {
                            echo "<tr>
                                            <td>" . htmlspecialchars($row['party_abbreviation']) . "</td>
                                            <td>" . htmlspecialchars($row['party_score']) . "</td>
                                          </tr>";
                            $total_votes += $row['party_score'];
                        }

                        echo "<tr>
                                        <th>Total Votes</th>
                                        <td><strong>" . $total_votes . "</strong></td>
                                      </tr>";
                        echo "</table>";
                    } else {
                        echo "<p>No results found for this polling unit.</p>";
                    }
                }

                $conn->close();
                ?>
                </select>
                </form>
                <!-- <form>
                <select name="polling_unit" id="polling_unit">
                    <option value="polling_unit">Select Polling Unit</option>
                    <button type="submit" name="view">View results</button>
                </select>
                </form> -->





                <section>
                    <div class="display-table">
                        <p><strong>Note:</strong> Select Polling units to view results</p>
                    </div>
                </section>
    </main>
    <footer>
        <p>&copy; 2025 Velastic</p>
    </footer>
</body>

</html>