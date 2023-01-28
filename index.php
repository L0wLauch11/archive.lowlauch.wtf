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
        <a href="/"><h1>Archiv</h1></a>
        <p>
            archive.lowlauch.wtf ist ein Archiv, welches unlimitierten Speicherplatz hat, indem es sich die Dienste von <a href="https://nft.storage/">https://nft.storage/</a> zu nutze macht. Hochladen kann man Dateien mit dem Archiv Uploader und zwei Zugangstokens die man von mir bekommen kann.
            <br><br>
            Der Uploader läuft unter Windows mit dem <a href="https://dotnet.microsoft.com/en-us/download/dotnet-framework/thank-you/net472-offline-installer">.NET Framework 4.7.2</a><br>oder unter Linux/MacOS/*BSD mit <a href="https://www.mono-project.com/">mono</a> und <a href="https://nodejs.org/en/download/">NodeJS</a>.
        </p>
        <a href="https://archive.lowlauch.wtf/?file=ArchivUploader.7z">Archiv Uploader download</a>

        <div class="seperator"></div>

        <?php

        function fileDiv($fileName, $fileDescription, $fileSizeInMB, $fileDate, $fileLink = -1) {
            if ($fileLink == -1)
                $fileLink = "?file=$fileName";

            return "<a href='$fileLink' target='_blank'>
                <div class='file'>
                    <div class='filename'>$fileName</div>

                    <br>

                    <div class='file-description'>$fileDescription</div>
                    <div class='file-size'>{$fileSizeInMB}MB</div>
                    <div class='file-date'>$fileDate</div>
                    
                </div>
            </a> ";
        }

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $ini_array = parse_ini_file("credentials.ini");

        $servername = $ini_array['servername'];
        $username = $ini_array['username'];
        $password = $ini_array['password'];
        $dbname = $ini_array['dbname'];

        $table = 'archive';

        function get_table($table, $conn)
        {
            $sql_all = "SELECT * FROM $table";
            return $conn->query($sql_all);
        }

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Add all listed files to an array to be shown later 
        $files_list = array();
        $files_to_download = array();

        $table_data = get_table($table, $conn);
        while ($row = $table_data->fetch_assoc()) {
            $fileURL = $row['fileURL'];
            $fileName = $row['fileName'];
            $fileDescription = $row['fileDescription'];
            $fileSizeInMB = $row['fileSizeInMB'];
            $fileDate = $row['fileDate'];
            $fileHidden = $row['fileHidden'];

            if (isset($_GET['file'])) {
                $requestedFile = $_GET['file'];
                if ($fileName == $requestedFile) {
                    array_push($files_to_download, $row);
                }
            }

            if ($fileHidden == 1)
                continue;

            if ($fileSizeInMB == 0)
                $fileSizeInMB = "&lt; 1";

            $file_div = fileDiv($fileName, $fileDescription, $fileSizeInMB, $fileDate);

            array_push($files_list, $file_div);
        }
        
        if (isset($_GET['file']))
        {
            // Multiple files with the same name handling
            if (count($files_to_download) > 1) {
                echo '<p style="text-align: center;">Es gibt mehr als eine Datei mit diesem Namen.<br><br><span style="font-size: 18px; font-weight: bold;">Wähle eine Aus!</span></p><div class="seperator"></div>';

                $fileURLsAlreadyListed = array();
                foreach ($files_to_download as $file)
                {
                    $fileURL = $file['fileURL'];

                    // Don't list the same file twice
                    if (in_array($fileURL, $fileURLsAlreadyListed))
                        continue;

                    array_push($fileURLsAlreadyListed, $fileURL);

                    $fileName = $file['fileName'];
                    $fileDescription = $file['fileDescription'];
                    $fileSizeInMB = $file['fileSizeInMB'];
                    $fileDate = $file['fileDate'];
                    $fileHidden = $file['fileHidden'];

                    echo fileDiv($fileName, $fileDescription, $fileSizeInMB, $fileDate, $fileURL);
                }
                return;
            } else
            {
                $fileURL = $files_to_download[0]['fileURL'];
                header("Location: $fileURL");
                die();
            }
        }

        // Show file array
        $files_list = array_reverse($files_list);

        foreach ($files_list as $file) {
            echo $file;
        }

        // Close MySQL Connection
        $conn->close();

        ?>

        <div class="seperator"></div>

        <a href="https://github.com/L0wLauch11/archive.lowlauch.wtf">source</a>
    </div>

</body>

</html>