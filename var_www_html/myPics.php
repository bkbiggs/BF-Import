<!DOCTYPE html>

<?php
$dbg = 'FALSE';
if ( isset($_REQUEST["dbg"]) && $_REQUEST["dbg"] != "" ) {
	$dbg = $_REQUEST ['dbg'];
}

$pi = "";
if ( isset($_REQUEST["pi"])  && $_REQUEST ["pi"] != "") {
	$pi = $_REQUEST ["pi"];
} else {
	$pi = "10.0.1.25";
}
// echo "pi = \"$pi\"\n";

$connection = ssh2_connect ( "$pi", 22 );
ssh2_auth_password ( $connection, 'bftest', 'bftest' );

$stream = ssh2_exec ( $connection, '/home/bftest/runme.sh' );

stream_set_blocking ( $stream, true );
$images = array ();
while ($o = fgets( $stream )) {
	$images [] = chop ( $o );
}

?>

<html lang="en">
<head>
<meta charset="UTF-8">

		<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
<!--    <link rel="stylesheet" type="text/css" href="./myPics.css">-->
		<link rel="stylesheet" href="css/bootstrap.min.css">
	<title>myPics Selection</title>

	<style>

body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
}

.top-container {
	background: #42A5F5;
	/* background-color: #2196F3; */
  /* background-color: #f1f1f1; */
  padding: 30px;
  text-align: center;
}

.header {
  padding: 10px 16px;
  /* background: #555; */
	color: #f1f1f1;
	background: LightBlue;
	/* opacity: 0.9; */
	opacity: 1.0;
}

.content {
  padding: 10px;
}

/** Footer **/
.footer {
   /* opacity: 0.9; */
   position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   background-color: LightBlue;
   color: white;
	 text-align: center;
}

.sticky {
  position: sticky;
  top: 0;
  width: 100%;
	z-index: +1;
}

.sticky + .content {
	padding-top: 0px;
}
</style>

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
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function saveImage(image) {

	// var processListStr = "";
	var saveListStr = image;
	document.write(image);
	sleep(10000);

	var myurl = "http://10.0.1.12/myPicsArrayPending.php";
	var myargs = {
		pi: <?php echo "\"$pi\"" ?>,
		functionname:"save",
		arguments: saveListStr
	};
		 
	$.ajax({
		type: "post",
		url: myurl,
		data: myargs, 
	});
 
}

function saveList() {

	// var processListStr = "";
	var saveListStr = "";
		
		console.log("document.forms.length=");
		console.log(document.forms.length);

	for (i=1; i<document.forms.length; i++) {
	  if (document.forms[i].elements[4].checked == true) {

	    mystr = document.forms[i].elements[1].value;
	    myarr = mystr.split("/");
			console.log("myarr[5]=");
			console.log(myarr[5]);
	    if (saveListStr == "") {
	      saveListStr = myarr[5];
	    } else {
	      saveListStr += "," + myarr[5];
	    }
	  }
	}
		
	var myurl = "http://10.0.1.12/myPicsArrayPending.php";
	var myargs = {
		pi: <?php echo "\"$pi\"" ?>,
		functionname:"save",
		arguments: saveListStr
	};
		
	$.ajax({
		type: "post",
		url: myurl,
		data: myargs, 
	});

}


function deleteList() {

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
	
 	var myurl = "http://10.0.1.12/myPicsArrayPending.php";
	var myargs = {
		pi: <?php echo "\"$pi\"" ?>,
		functionname:"delete",
		arguments: deleteListStr
	};
		
	$.ajax({
		type: "post",
		url: myurl,
		data: myargs, 
		// success: success
	});

}

</script>

</head>

<body>


<div class="top-container">
  <h1>Bird Feeder - Import selection</h1>
</div>

<div class="header" id="myHeader">
	<b>Images from </b>
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

			<input type="submit" value="Re-scan from this camera"> 

			<!--
			<input type="submit" onclick="deleteList()" value="Delete"> 
			<input type="submit" onclick="saveList()" value="Save"> 
			-->

		</form>
</div>


<div class="image content container">
<div class="row">

<!-- 
The Rules of the Grid:
1. Columns must be the immediate child of a Row.
2. Rows are only used to contain Columns, nothing else.
3. Rows should be placed inside a Container.

Those rules are very IMPORTANT. The Rows & Columns always work together, and you should never have one without the other.

Bad stuff will happen if you don’t follow those 3 simple Grid rules, exactly. I’ve answered countless Bootstrap questions on Stack Overflow by simply applying those rules. At first this might sound complicated, but it’s really easy once you understand how the Grid works.
-->

<?php
			$i = 0;
			// $y = count($images);

			for($x = 0; $x < count ( $images ); $x ++) {

				$imgs = explode ( '/', $images [$x] );
				$img = $imgs [4] . "/" . $imgs [5];
				$img_icon = $imgs [4] . "/icon_" . $imgs [5];

				$i ++;

				echo "<section class=\"col-sm-6 col-md-6 col-lg-4 col-xl-3\">\n";
				echo "<form id=\"myChoice\" style=\"text-align:center;\">\n";

				echo "#" . $i . "<br>\n";

				echo "<a href=\"http://" . $pi . "/" . $img . "\" target=\"_blank\">\n";
				echo "<img src=\"http://" . $pi . "/" . $img_icon . "\" class=\"thumbnail\" width=\"256\" height=\"192\">\n";
				echo "</a><br>\n";

				echo "<div style=\"font-size:18;\">" . $imgs [5] . "</div>\n";

				echo "<input type=\"hidden\" name=\"pi\" value=\"" . $pi . "\">\n";
				echo "<input type=\"hidden\" name=\"fn\" value=\"$images[$x]\">\n";
				echo "<input type=\"hidden\" name=\"dbg\" value=\"$dbg\">\n";

				echo "<p>";
				echo "Delete: ";
				echo "<input type=\"checkbox\" name=\"deleteId\" >";
				echo " &nbsp; Save: ";
				echo "<input type=\"checkbox\" name=\"saveId\" ></p>\n";

				echo "</form>\n";
				// echo "</figure>\n";
				echo "</section>\n";
			}

		?>


</div>
</div>


<div class="footer">
		<input type="submit" onclick="deleteList()" value="Delete"> 
		<input type="submit" onclick="saveList()" value="Save"> 
</div>


</div>

<script>

// When the user scrolls the page, execute myFunction 
window.onscroll = function() {myFunction()};

// Get the header
var header = document.getElementById("myHeader");

// Get the offset position of the navbar
var sticky = header.offsetTop;

// Add the sticky class to the header when you reach its scroll position. Remove "sticky" when you leave the scroll position
function myFunction() {
  if (window.pageYOffset > sticky) {
    header.classList.add("sticky");
  } else {
    header.classList.remove("sticky");
  }
}

</script>





</body>
</html>
