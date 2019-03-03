<?php
function copyFile($id, $host, $fn){

	$v2 = 0;
	$v3 = 0;
	
	$copyFile = "/usr/bin/scp ". $id . "@" . $host . ":$fn /var/www/html/share/images 2>&1";
	echo "copy: " . $copyFile . "<br>\n";
	echo exec( $copyFile, $v2, $v3);
	
	if (count ( $v2 ) > 0) {
		echo "$fn<br>\n";
		echo "Return value: " . $v2[0] . "<br>\n";
	}
	
	if ($v3 != 0) {
		echo "$fn<br>\n";
		echo "Return code: " . $v3 . "<br>\n";
	}
	
}

function deleteFile($id, $host, $fn) {
	
	$v2 = 0;
	$v3 = 0;
	
	$rmFile = "/usr/bin/ssh " . $id . "@" . $host . " 'rm " . $fn . " &2>1'";
	echo "del:  " . $rmFile . "<br>\n";
	echo exec ( $rmFile, $v2, $v3 );
	
	if (count ( $v2 ) > 0) {
		echo "$rmFile<br>\n";
		echo "Return value: " . $v2[0] . "<br>\n";
	}
	
	if ($v3 != 0) {
		echo "$rmFile<br>\n";
		echo "Return code: " . $v3 . "<br>\n";
	}
	
	
}

// header ( 'Content-Type: application/json' );

$aResult = array ();

// default user name being used on camera host
$id = "pi";

// location on camera host to get the images from
$newImageDir = "/home/pi/share/images";



if (! isset ( $_REQUEST ['functionname'] )) {
	$aResult ['error'] = 'No function name!';
}

if (! isset ( $_REQUEST ['arguments'] )) {
	$aResult ['error'] = 'No function arguments!';
}

echo "encode function: ";
echo json_encode ( $aResult );
echo "<br>\n";

//
//


$dbg = "FALSE";
if ($_REQUEST ["dbg"]) {
	$dbg = $_REQUEST ["dbg"];
} else {
	$dbg = "FALSE";
}


// Which camera machine are we working on?
if ($_REQUEST ["pi"]) {
	$cameraHost = $_REQUEST ["pi"];
} else {
	$cameraHost = "10.0.1.25";
}

if ($_REQUEST ["functionname"]) {
	$functionname = $_REQUEST["functionname"];
}

// array of files to be processed
$fna = $_REQUEST ["arguments"];


//
// $fna argument should be "<fn1>,<fn2>,..."
//   - should not have a path
//   - do not include icons, they will be generated here
//

$fnaa = explode ( ",", $fna);

//
// for each element in fna, do the following loop body
//

for($i = 0; $i < count ( $fnaa ); $i ++) {

	
	$fn = $fnaa [$i];
	
	if ($fn == "") {
		echo "No file specified.<br>\n";
		break;
	}
	
	// 
	// Think about how to proceed:
	//	- do we want to remove the files individually as their copied
	// or 
	//  - remove pairs of files, as they are copied as a pair
	//
	
	
	//
	// copy the icon file from the camera host to this host
	//
	$myicon = "$newImageDir/icon_" . $fn;
	
	if ( $functionname == "save") {
		copyFile($id, $cameraHost, $myicon);
	}
	
	if ( ($functionname == "save") || ($functionname == "delete")) {
		deleteFile($id, $cameraHost, $myicon);
	}
	
	//
	// copy the full image from the camera host to this host
	//
	$myfn = "$newImageDir/" . $fn;
	

	if ( $functionname == "save") {
		copyFile($id, $cameraHost, $myfn);
	}
	
	if ( ($functionname == "save") || ($functionname == "delete")) {
		deleteFile($id, $cameraHost, $myfn);
	}
		
	
}

$n = 10;
if ($dbg == true) {
	$n = 10000;
}
;

echo "<html>\n";
echo "    <body>\n";
echo "    <p>Saved!</p>\n";
echo "    <script>\n";
echo "        var timer = setTimeout(function() {\n";
echo "            window.location='http://10.0.1.12/myPics.php?pi=$cameraHost&dbg=$dbg'\n";
echo "        }, $n);\n";
echo "    </script>\n";
echo "</body>\n";
echo "</html>\n";


