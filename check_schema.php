<?php
define('BASEPATH', 'true');
require_once('application/config/database.php');
$db_config = $db['default'];

$conn = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DESCRIBE document_types";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo $row["Field"]. " - " . $row["Type"]. "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
?>
