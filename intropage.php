<?php

session_start();

if(!isset($_SESSION["session_username"])):
header("location:login.php");
else:
?>
	
<?php include("includes/header.php"); ?>
<div id="welcome">
<h2>welcome, <span><?php echo $_SESSION['session_username'];?>! </span></h2>
  <p><a href="logout.php">Exit</a> from system</p>
</div>
	
<?php include("includes/footer.php"); ?>
	
<?php endif; ?>
