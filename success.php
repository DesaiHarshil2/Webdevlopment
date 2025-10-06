<?php
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Registration Successful</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<section class="auth-section">
		<div class="container">
			<div class="auth-form-container" style="text-align:center;">
				<h2>Registration Successful</h2>
				<p>Welcome, <?php echo $name; ?>! Your registration has been recorded.</p>
				<a href="login.html" class="btn btn-primary" style="margin-top:16px; display:inline-block;">Go to Login</a>
			</div>
		</div>
	</section>
</body>
</html>

