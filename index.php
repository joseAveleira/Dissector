
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
            <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo" >Web Dissector</a></div>
        </nav>

        <div class="container" style="margin-top:20px;">
            <div class="row" >
                <form class="col s12" name='frm' method='post' class='form-horizontal' action='index.php' class='form-group ' enctype="multipart/form-data">
                    <div class="row">
                        <div class="input-field col s6" style="margin-bottom:20px;">
                            <div class="file-field ">
                                <div class="btn">
                                    <span>File</span>
                                    <input type="file" name="fichero_usuario">
                                </div>
                                <div class="file-path-wrapper">
                                    <input class="file-path validate" type="text" placeholder="Upload pcap file" required="">
                                </div>
                            </div>
                        </div>
                        <div class="input-field col s6" style="margin-bottom:20px;">
                            <i class="material-icons prefix">insert_drive_file</i>
                            <input type="text" class="validate" name="csvName" required="">
                            <label for="icon_telephone">CSV name</label>
                        </div>

                        <div class=" col s2">
                            <input type="checkbox" id="test1" name="typeFields[]" value="frameFields"/>
                            <label for="test1">Basic fields</label>
                        </div>
                        <div class=" col s2">
                            <input type="checkbox" id="test2" name="typeFields[]" value="mqttFields" />
                            <label for="test2">MQTT fields</label>
                        </div>
                        <div class=" col s2">
                            <input type="checkbox" id="test3" name="typeFields[]" value="awdFields" />
                            <label for="test3">Awd Dataset fields</label>
                        </div>

                    </div>
                    <button  id="checkBtn" type="submit" name="action"  class="waves-effect waves-light btn" style="float:right">Submit <i class="material-icons right">send</i></button>
                    <hr style="margin-top:120px;border-top: 2px solid #ddd " />
                    <h4 style="color:#26A69A">CSV Files</h4>
                </form>
            </div>
            <!-- <form name='frm' method='post' class='form-horizontal' action='index.php' class='form-group ' enctype="multipart/form-data">
                 <input type="file" name="fichero_usuario"><br /> Nombre del CSV: <input type="text" name="csvName"><br />
                 <input type="submit" value="Submit">
             </form> -->


            <div class="row">
                <?php
                require_once("functions.php");
                $haydatos = (count($_POST) > 0);
                if (!$haydatos) {
                    csvList();
                } else {



                    $protocols = $_POST['typeFields'];


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
                    //$protocols = array("framefields", "mqttFields");
                    // $protocols = array("test");
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

                    $nameFields = str_replace("__-e__", ",", $fields);
                    $nameFields = ltrim($nameFields, ",");
                    $nameFields = $nameFields . "\n";

                    //echo "<br/>".$nameFields."<br />";
                    $salida = shell_exec(dirname(__FILE__) . "/script.sh ./pcapFiles/{$namefile} ./csvFiles/{$nameCSV}.csv " . $fields);
                    //echo "CSV generado";
                    //$file_data = "Stuff you want to add\n";
                    $nameFields .= file_get_contents("./csvFiles/$nameCSV.csv");
                    file_put_contents("./csvFiles/$nameCSV.csv", $nameFields);
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
        <script type="text/javascript" src="js/myScript.js"></script>
    </body>
</html>
