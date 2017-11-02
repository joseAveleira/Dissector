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
