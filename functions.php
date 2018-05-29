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

    $tags = fopen("./tags/intrusion02.txt", "r") or die("Unable to open file!");
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


    //$conta =0;
    while (!feof($myfile)) {
        if ($firstLine) {
            $linea = fgets($myfile);
            $linea = preg_replace("[\n|\r|\n\r]", '', $linea) . ",type\n";
            $etiquetado .= $linea;
            $firstLine = FALSE;
        } else {
            $linea = fgets($myfile);

            if (!$linea == '') {
                /* $contenido = analisisTrama($linea, $times);
                  if($conta == 20)
                  {
                  //echo $contenido."< br />";
                  $var=str_getcsv($contenido);
                  echo count(explode("\",", $contenido));
                  echo "<br />";

                  echo "<br />";echo "<br />";echo "<pre>";
                  print_r($var);
                  echo "</pre>";echo "<br />";echo "<br />";echo "<br />";
                  }
                  $conta ++; */
                //$tramas = explode(",", $contenido);
                $etiquetado .= analisisTrama($linea, $times);
            }
        }

        // echo fgets($myfile);
    }
    fclose($myfile);
    file_put_contents($file, $etiquetado);
}

function analisisTrama($trama, $times) {
    $tramas = str_getcsv($trama);
    $lenght = count($times);
    //echo "<br /><br />".$lenght."<br /><br />";
    $timeTrama = floatval($tramas[2]);
    //echo $timeTrama;
    $linea = "";

    $comas = -2;
    $ncomas = -1;
    for ($r = 0; $r < count($tramas); $r++) {

        $ncomas = count(explode(",", $tramas[$r]));
        //echo " " . $ncomas . " ";
        if ($ncomas > $comas) {
            $comas = $ncomas;
        }
    }
    //echo "<br />";
    //echo "Total " . $comas;
    //echo "<br />";
    for ($i = 0; $i < $lenght; $i = $i + 2) {

        if ($timeTrama >= $times[$i] && $timeTrama <= $times[$i + 1]) {


            if ($comas > 1) {
                $lineaEspecial = "";
                for ($j = 0; $j < $comas; $j++) {
                    $concat = '';
                    for ($m = 0; $m < count($tramas); $m++) {
                        $desanidador = explode(",", $tramas[$m]);
                        if (isset($desanidador[$j])) {
                            $tramas[$m] = $desanidador[$j];
                        }


                        if ($m == count($tramas) - 1) {

                            $concat .= "\"" . $tramas[$m] . "\"" . ",intrusion\n";
                        } else {
                            $concat .= "\"" . $tramas[$m] . "\",";
                        }
                    }

                    $lineaEspecial .= $concat;
                }
                return $lineaEspecial;
            } else {
                return $linea = preg_replace("[\n|\r|\n\r]", '', $trama) . ",intrusion\n";
            }
        } else {



            //echo "normal- $timeTrama<br />";

        }
        //echo $times[$i+1];
        //echo $i."<br />";
    }
      if ($comas > 1) {
                $lineaEspecial = "";
                for ($j = 0; $j < $comas; $j++) {
                    $concat = '';
                    for ($m = 0; $m < count($tramas); $m++) {
                        $desanidador = explode(",", $tramas[$m]);
                        if (isset($desanidador[$j])) {
                            $tramas[$m] = $desanidador[$j];
                        }


                        if ($m == count($tramas) - 1) {

                            $concat .= "\"" . $tramas[$m] . "\"" . ",normal\n";
                        } else {
                            $concat .= "\"" . $tramas[$m] . "\",";
                        }
                    }

                    $lineaEspecial .= $concat;
                }
                return $lineaEspecial;
            } else {
                return $linea = preg_replace("[\n|\r|\n\r]", '', $trama) . ",normal\n";
            }
    //todoo inicio y fin de los timestamp
    //echo $tramas[2]."<br />";
   // return $linea;
}
