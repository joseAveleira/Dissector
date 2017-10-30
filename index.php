
<!DOCTYPE html>
<html>
    <head>
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
        <link type="text/css" rel="stylesheet" href="css/myCss.css"  media="screen,projection"/>
        <!--Let browser know website is optimized for mobile -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>

    <body>
        <nav class=" lighten-1" role="navigation" style="background-color: #26A69A">
            <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo" >pcap Web Dissector</a></div>
        </nav>

        <div class="container" style="margin-top:20px;   ">
            <div class="row" >
                <form class="col s12" name='frm' method='post' class='form-horizontal' action='index.php' class='form-group ' enctype="multipart/form-data">
                    <div class="row">
                        <div class="input-field col s6">
                            <div class="file-field ">
                                <div class="btn">
                                    <span>File</span>
                                    <input type="file" name="fichero_usuario">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="upload pcap file">
                                </div>
                            </div>
                        </div>
                        <div class="input-field col s6">
                            <i class="material-icons prefix">insert_drive_file</i>
                            <input type="text" class="validate" name="csvName">
                            <label for="icon_telephone">CSV name</label>
                        </div>
                    </div>
                    <button type="submit" name="action"  class="waves-effect waves-light btn">Submit</button>

                </form>
            </div>
            <!-- <form name='frm' method='post' class='form-horizontal' action='index.php' class='form-group ' enctype="multipart/form-data">
                 <input type="file" name="fichero_usuario"><br /> Nombre del CSV: <input type="text" name="csvName"><br />
                 <input type="submit" value="Submit">
             </form> -->


            <div class="row">
                <?php

                             
                function csvList() {
                    $archivo = scan_dir("./csvFiles"); //ruta actual
                    $tam= count($archivo);
                    for ($i=0;$i<$tam;$i++) { //obtenemos un archivo y luego otro sucesivamente
                        if (!is_dir($archivo[$i]) && ($archivo[$i] != ".." || $archivo[$i] != "." )) {//verificamos si es o no un directorio
                            echo "<a class='nounderline ' href='./csvFiles/$archivo[$i]' download><div class=\"col s2 csv  \" >$archivo[$i] <i class= 'material-icons prefix' style='float: right;'>cloud_download</i></div></a>";
                        }
                    }
                }

                function scan_dir($dir) {
                    
                    //https://stackoverflow.com/questions/11923235/scandir-to-sort-by-date-modified
                    $ignored = array('.', '..', '.svn', '.htaccess');

                    $files = array();
                    foreach (scandir($dir) as $file) {
                        if (in_array($file, $ignored) && !is_dir($file))
                            continue;
                        $files[$file] = filemtime($dir . '/' . $file);
                    }

                    arsort($files);
                    $files = array_keys($files);
                    $files = array_reverse($files);

                    return ($files) ? $files : false;
                }

                $haydatos = (count($_POST) > 0);
                if (!$haydatos) {
                    csvList();
                } else {

                    $dir_upload = 'pcapFiles/';
                    $namefile = $_FILES['fichero_usuario']['name'];

                    $nameCSV = $_POST['csvName'];
                    $fichero_subido = $dir_upload . basename($_FILES['fichero_usuario']['name']);

                    $tipeFileType = pathinfo($fichero_subido, PATHINFO_EXTENSION);

                    //Solo ficheros pcap
                    //echo $tipeFileType;

                    echo '<pre>';
                    if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
                        //echo "El fichero es válido y se subió con éxito.\n";
                    } else {
                        echo "Fallo al subir el fichero\n";
                    }

                    print "</pre>";
                    $protocols = array("framefields", "mqttFields");
                    $fields = "";
                    $lenghtProtocols = count($protocols);
                    for ($i = 0; $i < $lenghtProtocols; $i++) {
                        $myfile = fopen("FieldsProtocol/{$protocols[$i]}.txt", "r") or die("Unable to open file!");
                        // Output one line until end-of-file
                        while (!feof($myfile)) {
                            $fields .= "__-e__" . fgets($myfile);
                        }
                        fclose($myfile);
                    }
                    $fields = preg_replace("[\n|\r|\n\r]", '', $fields);
                    //echo $fields;

                    $salida = shell_exec(dirname(__FILE__) . "/script.sh ./pcapFiles/{$namefile} ./csvFiles/{$nameCSV}.csv " . $fields);
                    //echo "CSV generado";
                    csvList();
                }
                // tcpdump -i wlan0 -w /mnt/pendrive/IoTcaptura.pcap
                //echo "<pre>$salida</pre>";
                ?>
            </div>

        </div>
        <!--Import jQuery before materialize.js-->
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="js/materialize.min.js"></script>
    </body>
</html>