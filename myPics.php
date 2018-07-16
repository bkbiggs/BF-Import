<?php
$dbg = "FALSE";
if ($_REQUEST["dbg"]) {
   $dbg = $_REQUEST["dbg"];
}
echo "debug \"$dbg\"\n";

if ($_REQUEST["pi"]) {
   $pi=$_REQUEST["pi"];
}
else {
   $pi="10.0.1.25";
}

$connection = ssh2_connect("$pi",22);
ssh2_auth_password($connection, 'bftest', 'bftest');

$stream = ssh2_exec($connection, '/home/bftest/runme.sh');

?>

<html>
<head>


<style>
.grid-container {
  display: grid;
  grid-template-columns: auto auto auto;
  grid-template-rows: 350px 350px 350px 350px 350px 350px 350px;
  grid-gap: 5px;
  background-color: #2196F3;
  padding: 5px;  
  justify-content: space-around;
}
.grid-container>div {
  background-color: rgba(255, 255, 255, 0.8);
  text-align: center;
  padding:2px 2px;
  font-size: 24px
</style>


<title>myPics Selection</title>
</head>
<body>
<h1>Select to include in favorites</h1>
<p>

<?php

   stream_set_blocking($stream, true);
   $images = array();
   $imgnum = 0;
   while ($o=fgets($stream)) {

      $images[] = chop($o);
      $imgnum += 1;

   }
?>

Currently scanning images from <?php echo $pi; ?><p>

<form action="/myPics.php">
   <select id="whichPi" name="pi">
      <option value="10.0.1.25" <?php if ($pi=='10.0.1.25') {echo("selected");}?>>Raspberry15 (outside)</option>
      <option value="10.0.1.19" <?php if ($pi=='10.0.1.19') {echo("selected");}?>>Raspberry9 (inside #1)</option>
      <option value="10.0.1.26" <?php if ($pi=='10.0.1.26') {echo("selected");}?>>Raspberry16 (inside #2)</option>
   </select>

<script>
function setSelectedIndex(s, valsearch)
{

   for (i=0; i<s.options.length; i++)
   {
      if (s.options[i].value == valsearch)
      {
         s.options[i].selected = true;
         break;
      }
   }
   return;

}

setSelectedIndex(document.getElementById("whichPi"), $pi);

</script>

   <br>
   <input type="submit">
</form>


<div class="grid-container">

<?php
   $i = 0;
   $y = count($images);
   for ($x=0; $x<count($images) ; $x++) {
      $imgs = explode('/',$images[$x]);
      $img = $imgs[4]."/".$imgs[5];
      $img_icon = $imgs[4]."/icon_".$imgs[5];
      // echo "<div class=\"item".$i."\" >\n";
      echo "<div>";
      $i++;
      // if ($i > 3 ) { $i=1; }
      echo "<figure>\n";
      echo "<form>\n";
      echo "#".$i."<br>\n";
      echo "<a href=\"http://".$pi."/".$img."\" target=\"_blank\">";
      echo "<img src=\"http://".$pi."/".$img_icon."\" width=\"256\" height=\"192\">";
      echo "</a><br>";
      echo "<figcaption style=\"font-size:18\">".$imgs[5]."</figcaption>";
      echo "<input type=\"hidden\" name=\"pi\" value=\"".$pi."\">";
      echo "<input type=\"hidden\" name=\"fn\" value=\"$images[$x]\">";
      echo "<input type=\"hidden\" name=\"dbg\" value=\"$dbg\">";
      echo "<button type=\"submit\" value=\"Save\" formaction=\"./myPicsPending.php\">Save</button>&nbsp;";
      echo "<button type=\"submit\" value=\"Delete\" formaction=\"./myPicsDelete.php\">Delete</button>";
      echo "</form>\n";
      echo "</figure>\n";
      echo "</div>\n";
   }
?>

</div>



</body>
</html>
