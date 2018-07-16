<?php
$dbg = "FALSE";
if ($_REQUEST["dbg"]) {
    $dbg = $_REQUEST["dbg"];
}
echo "debug \"$dbg\"\n";


// Which camera machine are we working on?
if ($_REQUEST["pi"]) {
   $pi = $_REQUEST["pi"];
}
else {
   $pi = "10.0.1.25";
}

$fn   = $_REQUEST["fn"];
$desc = $_REQUEST["desc"];
if ($desc == "") {
   $desc = "new";
}

// transfer the file from an image source

if ($dbg) {echo "copy the file!<br>\n";}
if ($dbg) {echo "whoami = ".exec( 'whoami' )."<br>\n";}
if ($fn == "") {
   echo "No file specified.<br>\n";
}

$fns = explode("/", $fn);
$myicon = "/".$fns[1]."/".$fns[2]."/".$fns[3]."/".$fns[4]."/icon_".$fns[5];
if ($dbg) {echo $myicon."<br>\n";}

$copyFile = "/usr/bin/scp pi@".$pi.":$fn /var/www/html/share/images 2>&1";
if ($dbg) {echo $copyFile."<br>\n";}
echo exec( $copyFile);
if ($dbg) {echo "<br>\n";}

$copyFile = "/usr/bin/scp pi@".$pi.":$myicon /var/www/html/share/images 2>&1";
if ($dbg) {echo $copyFile."<br>\n";}
echo exec( $copyFile);
if ($dbg) {echo "<br>\n";}

// $chowncmd = "chgrp -R bf /var/www/html/share/images";
// echo $chowncmd."<br>\n";
// echo exec( $chwoncmd );
// echo "<br>\n";

$rmFile = "/usr/bin/ssh pi@".$pi." rm ".$fn;
if ($dbg) {echo $rmFile."<br>\n";}
echo exec( $rmFile );
if ($dbg) {echo "<br>\n";}


$rmFile = "/usr/bin/ssh pi@".$pi." rm ".$myicon;
if ($dbg) {echo $rmFile."<br>\n";}
echo exec( $rmFile );
if ($dbg) {echo "<br>\n";}

$n = 500;
if ($dbg == TRUE) { $n=2000; };

echo "<html>\n";
echo "    <body>\n";
echo "    <p>Saved!</p>\n";
echo "    <script>\n";
echo "        var timer = setTimeout(function() {\n";
echo "            window.location='http://10.0.1.12/myPics.php?pi=$pi&dbg=$dbg'\n";
echo "        }, $n);\n";
echo "    </script>\n";
echo "</body>\n";
echo "</html>\n";



?>