<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>yeahhyeahy</title>
    <link rel="stylesheet" href="style.css">
    <script src="uploadfile.js"></script>
</head>
<body>
    <div class="container">
        <h1>Archiv</h1>
        <!--
        <div class="subcontainer">
            <h2>Datei hochladen</h2>

            <input type="hidden" name="MAX_FILE_SIZE" value="107400000"> Max filesize: 100MB
            Datei: <input class="button" id="file-input" type="file" name="submit" value="Hochladen"><br><br>
            Passwort: <input class="textbox" type="password" name="password">
            <button class="button" onclick="uploadFile()">Hochladen</button>
            <p style="font-size: 12px;">Max. Dateigröße: 100MB</p>

        </div>
         -->

        <div class="seperator"></div>
        
        <?php

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $ini_array = parse_ini_file("credentials.ini");

        $servername = $ini_array['servername'];
        $username = $ini_array['username'];
        $password = $ini_array['password'];
        $dbname = $ini_array['dbname'];
        
        $table = 'archive';

        function get_table($table, $conn) {
            $sql_all = "SELECT * FROM $table";
            $result = $conn->query($sql_all);
            return $conn->query($sql_all);
        }

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $table_data = get_table($table, $conn);
        while ($row = $table_data->fetch_assoc()) {
            $fileURL = $row['fileURL'];
            $fileName = $row['fileName'];
            $fileDescription = $row['fileDescription'];
            $fileSizeInMB = $row['fileSizeInMB'];
            $fileDate = $row['fileDate'];

            if ($fileSizeInMB == 0)
                $fileSizeInMB = "< 1";

            echo "<a href='$fileURL' target='_blank'><div class='file'>$fileName<br><div class='file-description'>$fileDescription</div><div class='file-size'>{$fileSizeInMB}MB</div><div class='file-date'>$fileDate</div></div></a>";
        }

        $conn->close();

        ?>

        <div class="seperator"></div>

        <a style="color: chartreuse; text-decoration: underline;"href="https://github.com/L0wLauch11/archive.lowlauch.wtf">source</a>
    </div>
    
</body>

<script src="selectfile.js"></script>
</html>