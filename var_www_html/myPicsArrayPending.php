<?php
function myLogger($myMessage, $level) {

	$date = date("Y-m-d h:m:s");
	$file = __FILE__;
 
	// $message = "[{$date}] [{$file}] [{$level}] [{$myMessage}]".PHP_EOL;
	$message = "[{$file}] [{$level}] [{$myMessage}]";
	// log to our default location
	error_log($message);
}

function copyFile($id, $host, $fn){

	$v2 = 0;
	$v3 = 0;
	
	$copyFile = "/usr/bin/scp ". $id . "@" . $host . ":$fn /var/www/html/share/images 2>&1";
	myLogger ("copy: " . $copyFile, "info");
	myLogger ("exec: " . exec( $copyFile, $v2, $v3), "info");
	
	if (count ( $v2 ) > 0) {
		myLogger ("v2: $fn", "warning");
		myLogger ("v2: Return value: " . $v2[0], "warning" );
	}
	
	if ($v3 != 0) {
		myLogger ("v3: $fn", "info");
		myLogger ("v3: Return code: " . $v3, "warning" );
	}
	
}

function deleteFile($id, $host, $fn) {
	
	$v2 = 0;
	$v3 = 0;
	
	$rmFile = "/usr/bin/ssh " . $id . "@" . $host . " 'rm " . $fn . " &2>1'";
	myLogger ("del:  " . $rmFile, "info");
	myLogger ("exec: " . exec ( $rmFile, $v2, $v3 ), "info");
	
	if (count ( $v2 ) > 0) {
		myLogger ("v2: $rmFile", "warning");
		myLogger ("v2: Return value: " . $v2[0], "warning" );
	}
	
	if ($v3 != 0) {
		myLogger ("v3: $rmFile", "info");
		myLogger ("v3: Return code: " . $v3, "warning" );
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
if ( isset($_REQUEST["dbg"]) && $_REQUEST["dbg"] != "" ) {
	$dbg = $_REQUEST ["dbg"];
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


