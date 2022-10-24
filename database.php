<?php
$ini_array = parse_ini_file("credentials.ini");

$servername = $ini_array['servername'];
$username = $ini_array['username'];
$password = $ini_array['password'];
$dbname = $ini_array['dbname'];
$security_string = $ini_array['access_code'];

$table = 'archive';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$access_code = $_GET['a'];
if ($access_code != $security_string) {
    echo 'passwort falsch';
    return;
}

$operation = $_GET['o'];

switch ($operation) {
    case 'inittable':
        $sql = "CREATE TABLE IF NOT EXISTS $table (fileName VARCHAR(255), fileSizeInMB INT(100), fileDescription VARCHAR(1000), fileURL VARCHAR(2048), fileDate VARCHAR(2048))";
        $conn->query($sql);

        echo 'Done';

        break;

    case 'linkfile':
        $fileName = $_GET['n'];
        $fileSize = $_GET['s'];
        $fileDescription = $_GET['d'];
        $fileURL = $_GET['u'];
        $fileDate = $_GET['v'];

        $sql = "INSERT INTO $table (fileName, fileSizeInMB, fileDescription, fileURL, fileDate) VALUES ('$fileName', '$fileSize', '$fileDescription', '$fileURL', '$fileDate')";
        $result = $conn->query($sql);

        echo 'Done';
        
        break;

    default:
        echo 'Not a valid operation';
    
        break;
}

$conn->close();
?>