<!DOCTYPE html>

<?php
$dbg = 'FALSE';
if ($_REQUEST ['dbg']) {
	$dbg = $_REQUEST ['dbg'];
}
// echo "debug \"$dbg\"\n";

$pi = "";
if ($_REQUEST ['pi']) {
	$pi = $_REQUEST ["pi"];
} else {
	$pi = "10.0.1.25";
}
// echo "pi = \"$pi\"\n";

$connection = ssh2_connect ( "$pi", 22 );
ssh2_auth_password ( $connection, 'bftest', 'bftest' );

$stream = ssh2_exec ( $connection, '/home/bftest/runme.sh' );

?>

<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="./myPics.css">
	<title>myPics Selection</title>
</head>

<body>
	<h1>Select to include in favorites</h1>
	<p />

<?php
stream_set_blocking ( $stream, true );
$images = array ();
while ($o = fgets( $stream )) {
	$images [] = chop ( $o );
}
?>

Currently scanning images from <?php echo $pi; ?>
	
	<p />
	
	<form action="/myPics.php">
		<select id="whichPi" name="pi">
			<option value="10.0.1.25"
				<?php if ($pi=='10.0.1.25') {echo("selected");}?>>Raspberry15
				(outside)</option>
			<option value="10.0.1.19"
				<?php if ($pi=='10.0.1.19') {echo("selected");}?>>Raspberry9 (inside
				#1)</option>
			<option value="10.0.1.26"
				<?php if ($pi=='10.0.1.26') {echo("selected");}?>>Raspberry16
				(inside #2)</option>
		</select>

		<p />
		<input type="submit" value="Scan from this camera"> 
		<input type="submit" onclick="deleteList()" value="Delete"> 
		<input type="submit" onclick="saveList()" value="Save"> 
	</form>

	<p />


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"/>


<script>	
function setSelectedIndex(s, valsearch){

   for (i=0; i<s.options.length; i++){
      if (s.options[i].value == valsearch){
         s.options[i].selected = true;
         break;
      }
   }
   return;

}

setSelectedIndex(document.getElementById("whichPi"), <?php echo "\"$pi\""?>);

</script>


<script>

function saveList() {

	   // var processListStr = "";
	   var saveListStr = "";
		
	   for (i=1; i<document.forms.length; i++) {
	      if (document.forms[i].elements[4].checked == true) {

	         mystr = document.forms[i].elements[1].value;
	         myarr = mystr.split("/");
	         if (saveListStr == "") {
	             saveListStr = myarr[5];
	         } else {
	             saveListStr += "," + myarr[5];
	         }
	      }
	   }


		// build my request
		// code: myPicsArrayPending.php
		// host: $p1
		// functionname: process
		// arguments: list from saveListStr built just above
		
		// saveListStr = chr(34) + saveListStr + chr(34);
		
	 	var myurl = "http://10.0.1.12/myPicsArrayPending.php";
		var myargs = {
			pi: <?php echo "\"$pi\"" ?>,
			functionname:"save",
			arguments: saveListStr
		};

		// basically a visual info point...
// 		document.getElementById("demo1").innerHTML = 
// 			"url: \"" + myurl + "\"<br>" +
// 			"functionname: \"save\"<br>" +
// 			"arguments: " + saveListStr + "<br>";

			
		$.ajax({
			type: "post",
			url: myurl,
			data: myargs, 
		});

	}


function deleteList() {

//   var deleteListStr = "<p>Delete List<br>length = " + document.forms.length + "<br>";

   var deleteListStr = "";
	
   for (i=1; i<document.forms.length; i++) {
      if (document.forms[i].elements[3].checked == true) {

         mystr = document.forms[i].elements[1].value;
         myarr = mystr.split("/");
         if (deleteListStr == "") {
             deleteListStr = myarr[5];
         } else {
             deleteListStr += "," + myarr[5];
         }
      }
   }


	// build my request
	// code: myPicsArrayDelete.php
	// host: $p1
	// functionname: delete
	// arguments: list from deleteListStr built just above
	
	// deleteListStr = chr(34) + deleteListStr + chr(34);
	
 	var myurl = "http://10.0.1.12/myPicsArrayPending.php";
	var myargs = {
		pi: <?php echo "\"$pi\"" ?>,
		functionname:"delete",
		arguments: deleteListStr
	};

	// basically a visual info point...
// 	document.getElementById("demo1").innerHTML = 
// 		"url: \"" + myurl + "\"<br>" +
// 		"functionname: \"delete\"<br>" +
// 		"arguments: " + deleteListStr + "<br>";

		
	$.ajax({
		type: "post",
		url: myurl,
		data: myargs, 
		// success: success
	});

}


</script>


<!-- 	<p id="demo1">Delete List</p> -->
<!-- 	<p /> -->
 
<!-- 	<p id="demo2">Save List</p> -->
<!-- 	<p /> -->

	<div class="grid-container">

<?php
$i = 0;
// $y = count($images);
for($x = 0; $x < count ( $images ); $x ++) {

	$imgs = explode ( '/', $images [$x] );
	$img = $imgs [4] . "/" . $imgs [5];
	$img_icon = $imgs [4] . "/icon_" . $imgs [5];

	$i ++;

	echo "<div>";
	echo "<figure>\n";
	echo "<form id=\"myChoice\">\n";

	echo "#" . $i . "<br>\n";

	echo "<a href=\"http://" . $pi . "/" . $img . "\" target=\"_blank\">\n";
	echo "<img src=\"http://" . $pi . "/" . $img_icon . "\" class=\"thumbnail\" width=\"256\" height=\"192\">\n";
	echo "</a><br>";

	echo "<figcaption style=\"font-size:18\">" . $imgs [5] . "</figcaption>";

	echo "<input type=\"hidden\" name=\"pi\" value=\"" . $pi . "\">";
	echo "<input type=\"hidden\" name=\"fn\" value=\"$images[$x]\">";
	echo "<input type=\"hidden\" name=\"dbg\" value=\"$dbg\">";

	echo "<p>";
	echo " Delete: ";
	echo "<input type=\"checkbox\" name=\"deleteId\" >";
	echo " - Save: ";
	echo "<input type=\"checkbox\" name=\"saveId\" >";

	echo "</form>\n";
	echo "</figure>\n";
	echo "</div>\n";
}
?>

</div>



</body>
</html>
