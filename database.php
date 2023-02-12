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
        $sql = "CREATE TABLE IF NOT EXISTS $table (fileName VARCHAR(255), fileSizeInMB INT(100), fileDescription VARCHAR(1000), fileURL VARCHAR(2048), fileDate VARCHAR(2048), fileHidden INT(100))";
        $conn->query($sql);

        echo 'Done';

        break;

    case 'getfiles':
        $sql = "SELECT * FROM $table";
        $result = $conn->query($sql);

        $files_list = array();

        $table_data = $conn->query($sql);
        while ($row = $table_data->fetch_assoc()) {
            array_push($files_list, $row['fileName'] . "\n");
        }
        $files_list = array_reverse($files_list);

        foreach ($files_list as $file) {
            print $file;
        }

        break;

    case 'deletefile':
        $fileName = $_GET['n'];

        $sql = "DELETE FROM $table WHERE fileName='$fileName'";
        $conn->query($sql);

        print 'Done';

        break;

    case 'linkfile':
        $fileName = $_GET['n'];
        $fileSize = $_GET['s'];
        $fileDescription = $_GET['d'];
        $fileURL = $_GET['u'];
        $fileDate = $_GET['v'];
        $fileHidden = $_GET['h'];
        $fileOverwrite = $_GET['e'];

        if ($fileOverwrite == 1) {
            $sqlFileExists = "SELECT * FROM $table WHERE fileName='$fileName'";
            $result = $conn->query($sqlFileExists);

            if (mysqli_num_rows($result) > 0) {
                $sql = "DELETE FROM $table WHERE fileName='$fileName'";
                $conn->query($sql);
            }
        }

        $sql = "INSERT INTO $table (fileName, fileSizeInMB, fileDescription, fileURL, fileDate, fileHidden) VALUES ('$fileName', '$fileSize', '$fileDescription', '$fileURL', '$fileDate', '$fileHidden')";
        $conn->query($sql);

        echo 'Done';
        
        break;

    default:
        echo 'Not a valid operation';
    
        break;
}

$conn->close();
?>