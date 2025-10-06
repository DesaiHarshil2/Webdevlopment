<?php
$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'An error occurred.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Error</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<section class="auth-section">
		<div class="container">
			<div class="auth-form-container" style="text-align:center;">
				<h2>Submission Error</h2>
				<p><?php echo $msg; ?></p>
				<a href="signup.html" class="btn btn-secondary" style="margin-top:16px; display:inline-block;">Back to Sign Up</a>
			</div>
		</div>
	</section>
</body>
</html>

