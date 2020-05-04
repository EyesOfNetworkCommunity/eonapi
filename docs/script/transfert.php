<?php
echo "Start : \n";

function create_md($name,$content){
    $today = date("Y-m-d"); 
    $nom_file = "../_posts/".$today."-".$name."-auto.md";
    // création du fichier
    $f = fopen($nom_file, "wbx+");
    // écriture
    fputs($f, $content );
    // fermeture
    fclose($f);
}
echo "read patron: \n";
$patron  = file_get_contents('patron.md');
echo "Start csv describing\n";
if(isset($argv[1])){
    $row = 2;
    if (($handle = fopen($argv[1], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $content = $patron;
            $num = count($data);
            $content = str_replace('$FONCTION$', $data[0], $content);
            $content = str_replace('$NAME$', $data[0], $content);
            $content = str_replace('$TYPE$', $data[1], $content);
            $body = str_replace(',',",\r\n  ",$data[2]);
            $content = str_replace('$BODY$', $body, $content);
            $resp = str_replace(',',",\r\n  ",$data[3]);
            $content = str_replace('$RESPONSE$', $resp, $content);
            $content = str_replace('$COMMENT$', $data[4], $content);
            $content = str_replace('$CATEGORY$', $data[5], $content);
            create_md($data[0],$content);
            $row++;
        }
        fclose($handle);
    }
    echo "End nb file created = ".$row;
    exit(0);
    
}else{
    echo "No available data filed.\n";
    exit(1);
}

?>