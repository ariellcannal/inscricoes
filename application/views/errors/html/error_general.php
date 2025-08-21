<?php
global $html_errors;

defined('BASEPATH') or exit('No direct script access allowed');

if(!$html_errors){
    echo PHP_EOL.$heading.PHP_EOL.$message.PHP_EOL;
}
else{
    ?>

<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta charset="utf-8">
<title><?php echo $heading; ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
	integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
	integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
	integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
	integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<link rel="stylesheet" href="/assets/css/erro.css">
</head>
<body class="text-center">
	<div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
		<main role="main" class="inner cover">
			<h1 class="cover-heading"><?php echo $heading; ?></h1>
			<p class="lead"><?php echo $message; ?></p>
		</main>
	</div>
</body>
</html>
<?php }?>