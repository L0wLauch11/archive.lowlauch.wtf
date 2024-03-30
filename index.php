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
        <a href="/">
            <h1>Archiv</h1>
        </a>

        <div class="info">
            <p>
                archive.lowlauch.wtf ist ein Archiv, welches unlimitierten Speicherplatz hat, indem es sich die Dienste
                von <a href="https://nft.storage/">https://nft.storage/</a> zu nutze macht. Hochladen kann man Dateien
                mit dem Archiv Uploader und zwei Zugangstokens die man von mir bekommen kann.
                <br><br>
                Der Uploader läuft unter Windows mit dem <a
                    href="https://dotnet.microsoft.com/en-us/download/dotnet-framework/thank-you/net472-offline-installer">.NET
                    Framework 4.7.2</a><br>oder unter Linux/MacOS/*BSD mit <a
                    href="https://www.mono-project.com/">mono</a> und <a
                    href="https://nodejs.org/en/download/">NodeJS</a>.
            </p>
            <h3><a href="/?file=ArchivUploader.7z">Archiv Uploader download</a></h3>
        </div>

        <div class="seperator-small-bottom"></div>

        <div class="category-select">
            <ul>
                <li><a href="/">Alle</a></li>
                <li><a href="/?filetypes=mp4,m4a,flv,avi,webm,mov,mkv">Videos</a></li>
                <li><a href="/?filetypes=exe,appimage,py,mpy,apk">Applikationen</a></li>
                <li><a href="/?filetypes=png,webp,gif,apng,jpg,jpeg,qoi,svg,heic,heif,raw,tiff,psd,pdn,bmp">Grafiken</a>
                </li>
                <li><a href="/?filetypes=zip,7z,tar,xz,gz,rar">Archive</a></li>
                <li><a href="/?filetypes=docx,doc,pdf">Dokumente</a></li>
            </ul>
        </div>

        <div class="seperator-small-top"></div>

        <?php

        function file_div($file_name, $file_description, $file_size_in_mb, $file_date, $file_link = -1)
        {
            if ($file_link == -1)
                $file_link = "?file=$file_name";

            $big_file_style = "";
            if ($file_size_in_mb >= 500)
                $big_file_style = "style='color: red;'";
            

            $str = "<a href='$file_link' target='_blank'>
                <div class='file'>
                    <div class='filename'>$file_name</div>

                    <br>

                    <div class='file-description'>$file_description</div>
                    <div class='file-size' $big_file_style>{$file_size_in_mb}mb</div>
                    <div class='file-date'>$file_date</div>
                    
                </div>
            </a>";

            return $str;
        }

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(e_all);

        $ini_array = parse_ini_file("credentials.ini");

        $servername = $ini_array['servername'];
        $username = $ini_array['username'];
        $password = $ini_array['password'];
        $dbname = $ini_array['dbname'];

        $table = 'archive';

        function get_table($table, $conn)
        {
            $sql_all = "select * from $table";
            return $conn->query($sql_all);
        }

        // create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // check connection
        if ($conn->connect_error) {
            die("connection failed: " . $conn->connect_error);
        }

        // add all listed files to an array to be shown later 
        $files_list = array();
        $files_to_download = array();

        $table_data = get_table($table, $conn);
        while ($row = $table_data->fetch_assoc()) {
            $file_url = $row['fileURL'];
            $file_name = $row['fileName'];
            $file_description = $row['fileDescription'];
            $file_size_in_mb = $row['fileSizeInMB'];
            $file_date = $row['fileDate'];
            $file_hidden = $row['fileHidden'];
            $file_type = explode('.', $file_name)[count(explode('.', $file_name)) - 1]; // xd
        
            if (isset($_get['filetypes'])) {
                $allowed_filetypes = $_get['filetypes'];

                // for php < 8
                if (!function_exists('str_contains')) {
                    function str_contains($haystack, $needle)
                    {
                        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
                    }
                }

                if (!str_contains(strtolower($allowed_filetypes), strtolower($file_type)))
                    continue;
            }

            if (isset($_get['file'])) {
                $requested_file = $_get['file'];
                if ($file_name == $requested_file) {
                    array_push($files_to_download, $row);
                }
            }

            if ($file_hidden == 1)
                continue;

            if ($file_size_in_mb == 0)
                $file_size_in_mb = "&lt; 1";

            $file_div = file_div($file_name, $file_description, $file_size_in_mb, $file_date);

            array_push($files_list, $file_div);
        }

        if (isset($_get['file'])) {
            // multiple files with the same name handling
            if (count($files_to_download) > 1) {
                echo '<p style="text-align: center;">es gibt mehr als eine datei mit diesem namen.<br><br><span style="font-size: 18px; font-weight: bold;">wähle eine aus!</span></p><div class="seperator"></div>';

                $file_ur_ls_already_listed = array();
                foreach ($files_to_download as $file) {
                    $file_url = $file['file_url'];

                    // don't list the same file twice
                    if (in_array($file_url, $file_ur_ls_already_listed))
                        continue;

                    array_push($file_ur_ls_already_listed, $file_url);

                    $file_name = $file['file_name'];
                    $file_description = $file['file_description'];
                    $file_size_in_mb = $file['file_size_in_mb'];
                    $file_date = $file['file_date'];
                    $file_hidden = $file['file_hidden'];

                    echo file_div($file_name, $file_description, $file_size_in_mb, $file_date, $file_url);
                }
                return;
            } else {
                $file_url = $files_to_download[0]['file_url'];
                header("location: $file_url");
                die();
            }
        }

        // show file array
        $files_list = array_reverse($files_list);

        foreach ($files_list as $file) {
            echo $file;
        }

        if (count($files_list) == 0) {
            echo "keine dateien gefunden.";
        }

        // close my_sql connection
        $conn->close();

        ?>

        <div class="seperator"></div>

        <a href="https://github.com/L0wLauch11/archive.lowlauch.wtf">source</a>
    </div>

</body>

</html>