<?php
include 'database.php';

// Get all wards
$wardQuery = "SELECT ward_id, ward_name FROM ward";
$wardResult = $conn->query($wardQuery);

// Get all LGAs
$lgaQuery = "SELECT lga_id, lga_name FROM lga";
$lgaResult = $conn->query($lgaQuery);

// Get all parties
$partyQuery = "SELECT partyid, partyname FROM party";
$partyResult = $conn->query($partyQuery);

// Process form submission
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // First insert the new polling unit
    $polling_unit_name = $_POST['polling_unit_name'];
    $polling_unit_number = $_POST['polling_unit_number'];
    $polling_unit_description = $_POST['polling_unit_description'];
    $ward_id = $_POST['ward_id'];
    $lga_id = $_POST['lga_id'];
    $lat = $_POST['lat'];
    $long = $_POST['long'];
    $entered_by_user = $_POST['entered_by_user'];
    
    // Generate unique ward ID (combining the ward and LGA)
    $uniquewardid = $lga_id . $ward_id;
    
    // Let's debug the query
    // Insert into polling_unit table - FIXED to match your database structure
    $insertPUQuery = "INSERT INTO polling_unit 
                     (polling_unit_id, ward_id, lga_id, uniquewardid, polling_unit_number, 
                      polling_unit_name, polling_unit_description, lat, `long`, entered_by_user, date_entered) 
                     VALUES 
                     (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($insertPUQuery);
    
    // Check if prepare was successful
    if ($stmt === false) {
        $errorMessage = "Error in SQL prepare: " . $conn->error;
    } else {
        // Prepare was successful, proceed with binding parameters
        $stmt->bind_param("iiissssss", 
                        $ward_id, $lga_id, $uniquewardid, $polling_unit_number, 
                        $polling_unit_name, $polling_unit_description, $lat, $long, $entered_by_user);
        
        if ($stmt->execute()) {
            // Get the newly inserted polling unit ID
            $polling_unit_uniqueid = $conn->insert_id;
            
            // Next, insert the results for each party
            $insertSuccess = true;
            
            foreach ($_POST['party_score'] as $party_abbreviation => $party_score) {
                if (!empty($party_score)) {
                    $insertResultQuery = "INSERT INTO announced_pu_results 
                                        (result_id, polling_unit_uniqueid, party_abbreviation, party_score, 
                                        entered_by_user, date_entered) 
                                        VALUES 
                                        (NULL, ?, ?, ?, ?, NOW())";
                    
                    $resultStmt = $conn->prepare($insertResultQuery);
                    
                    if ($resultStmt === false) {
                        $insertSuccess = false;
                        $errorMessage = "Error preparing result insert: " . $conn->error;
                        break;
                    }
                    
                    $resultStmt->bind_param("isis", 
                                        $polling_unit_uniqueid, $party_abbreviation, $party_score, $entered_by_user);
                    
                    if (!$resultStmt->execute()) {
                        $insertSuccess = false;
                        $errorMessage = "Error saving party results: " . $resultStmt->error;
                        break;
                    }
                    $resultStmt->close();
                }
            }
            
            if ($insertSuccess) {
                $successMessage = "New polling unit and results added successfully!";
            }
        } else {
            $errorMessage = "Error adding polling unit: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Polling Unit Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        h1, h2 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, input, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .party-results {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            padding: 10px;
            background-color: #dff0d8;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            padding: 10px;
            background-color: #f2dede;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Polling Unit Results</h1>
        
        <?php if (!empty($successMessage)): ?>
            <div class="success"><?php echo $successMessage; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>1. Polling Unit Information</h2>
            
            <div class="form-group">
                <label for="polling_unit_name">Polling Unit Name:</label>
                <input type="text" id="polling_unit_name" name="polling_unit_name" required>
            </div>
            
            <div class="form-group">
                <label for="polling_unit_number">Polling Unit Number:</label>
                <input type="text" id="polling_unit_number" name="polling_unit_number" required>
            </div>
            
            <div class="form-group">
                <label for="polling_unit_description">Description:</label>
                <textarea id="polling_unit_description" name="polling_unit_description" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label for="ward_id">Select Ward:</label>
                <select id="ward_id" name="ward_id" required>
                    <option value="">-- Select Ward --</option>
                    <?php 
                    if ($wardResult && $wardResult->num_rows > 0) {
                        while($ward = $wardResult->fetch_assoc()): ?>
                            <option value="<?php echo $ward['ward_id']; ?>"><?php echo $ward['ward_name']; ?></option>
                        <?php endwhile;
                    } else {
                        echo "<option value=''>No wards found</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="lga_id">Select LGA:</label>
                <select id="lga_id" name="lga_id" required>
                    <option value="">-- Select LGA --</option>
                    <?php 
                    if ($lgaResult && $lgaResult->num_rows > 0) {
                        while($lga = $lgaResult->fetch_assoc()): ?>
                            <option value="<?php echo $lga['lga_id']; ?>"><?php echo $lga['lga_name']; ?></option>
                        <?php endwhile;
                    } else {
                        echo "<option value=''>No LGAs found</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="lat">Latitude:</label>
                <input type="text" id="lat" name="lat" placeholder="e.g. 5.12345678">
            </div>
            
            <div class="form-group">
                <label for="long">Longitude:</label>
                <input type="text" id="long" name="long" placeholder="e.g. 6.12345678">
            </div>
            
            <div class="form-group">
                <label for="entered_by_user">Entered By:</label>
                <input type="text" id="entered_by_user" name="entered_by_user" required>
            </div>
            
            <h2>2. Party Results</h2>
            <div class="party-results">
                <?php 
                // Check if there are parties in the database
                if ($partyResult && $partyResult->num_rows > 0) {
                    // Reset the party result cursor to beginning
                    $partyResult->data_seek(0);
                    while($party = $partyResult->fetch_assoc()): 
                    ?>
                        <div class="form-group">
                            <label for="party_<?php echo $party['partyid']; ?>"><?php echo $party['partyname']; ?> (<?php echo $party['partyid']; ?>):</label>
                            <input type="number" id="party_<?php echo $party['partyid']; ?>" name="party_score[<?php echo $party['partyid']; ?>]" min="0" value="0">
                        </div>
                    <?php endwhile;
                } else {
                    // Fallback: Manually list parties from your announced_pu_results table
                    $knownParties = ['PDP', 'DPP', 'ACN', 'PPA', 'CDC', 'JP'];
                    foreach ($knownParties as $party) {
                    ?>
                        <div class="form-group">
                            <label for="party_<?php echo $party; ?>"><?php echo $party; ?>:</label>
                            <input type="number" id="party_<?php echo $party; ?>" name="party_score[<?php echo $party; ?>]" min="0" value="0">
                        </div>
                    <?php
                    }
                }
                ?>
            </div>
            
            <button type="submit">Save Polling Unit Results</button>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>