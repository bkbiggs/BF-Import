<?php

// Which camera machine are we working on?
if ($_REQUEST["pi"]) {
   $pi = $_REQUEST["pi"];
}
else {
   $pi = "10.0.1.25";
}

$fn   = $_REQUEST["fn"];


// transfer the file from an image source

echo "delete the file!<br>\n";
echo "whoami = ".exec( 'whoami' )."<br>\n";
if ($fn == "") {
   echo "No file specified.<br>\n";
}



$rmFile = "/usr/bin/ssh pi@".$pi." rm ".$fn;
echo $rmFile."<br>\n";

echo exec( $rmFile, $array, $retval );
echo "retval = " . $retval . "<br>\n";
echo "<br>\n";



echo "<html>\n";
echo "    <body>\n";
echo "    <p>You will be redirected in 2 seconds</p>\n";
echo "    <script>\n";
echo "        var timer = setTimeout(function() {\n";
echo "            window.location='http://10.0.1.12/myPics.php?pi=$pi'\n";
echo "        }, 2000);\n";
echo "    </script>\n";
echo "</body>\n";
echo "</html>\n";


?>