<?php

function csvList() {
    $archivo = scan_dir("./csvFiles"); //ruta actual
    $tam = count($archivo);
    for ($i = 0; $i < $tam; $i++) { //obtenemos un archivo y luego otro sucesivamente
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

function etiquetado($file) {

    $tags = fopen("./tags/MITM.txt", "r") or die("Unable to open file!");
    // Output one line until end-of-file

    $times;
    $count = 0;
    while (!feof($tags)) {

        $linea = fgets($tags);

        $timestamp = explode(":", $linea);
        $times[$count] = floatval($timestamp[1]);
        $count++;
        //echo floatval($timestamp[1])."<br />";
    }
    fclose($tags);


    $myfile = fopen($file, "r") or die("Unable to open file!");
    // Output one line until end-of-file
    $etiquetado = "";
    $firstLine = TRUE;
    while (!feof($myfile)) {
        if ($firstLine) {
            $linea = fgets($myfile);
            $linea = preg_replace("[\n|\r|\n\r]", '', $linea) . ",type\n";
            $etiquetado .= $linea;
            $firstLine = FALSE;
        } else {
            $linea = fgets($myfile);

            if (!$linea == '') {
                $etiquetado .= analisisTrama($linea, $times);
            }
        }

        // echo fgets($myfile);
    }
    fclose($myfile);
    file_put_contents($file, $etiquetado);
}

function analisisTrama($trama, $times) {
    $tramas = explode(",", $trama);
    $lenght = count($times);
    //echo "<br /><br />".$lenght."<br /><br />";

    $timeTrama = floatval(substr($tramas[2], 1, -1));
    //echo $timeTrama;
    $linea = "";
    for ($i = 0; $i < $lenght; $i = $i + 2) {
        if ($timeTrama >= $times[$i] && $timeTrama <= $times[$i + 1]) {
           // echo "intrusion- $timeTrama<br />";
             return $linea = preg_replace("[\n|\r|\n\r]", '', $trama) . ",mitm\n";
        }
        else{
            //echo "normal- $timeTrama<br />";
           $linea = preg_replace("[\n|\r|\n\r]", '', $trama) . ",normal\n";
        }


        //echo $times[$i+1];
        //echo $i."<br />";
    }
    //todoo inicio y fin de los timestamp
    //echo $tramas[2]."<br />";
    return $linea;
}
