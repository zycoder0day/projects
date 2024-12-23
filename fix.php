<?php
ob_start ('ob_gzhandler'); 
  include "config/koneksi.php";
  include "main/rss.php";
  $iden=mysqli_fetch_array(mysqli_query($con,"SELECT * FROM identitas"));
  header("location: $iden[url]"); 
?>
