<?php
	if ("POST" === $_SERVER['REQUEST_METHOD']) {
		if (empty($_POST['targetRank'])) {
			$message = "Please select a rank to plan for.";
		} else {
			$message = "Congratulations on planning for your " . $_POST["targetRank"] . " rank";
		}
	}
?>
<html>
	<head>
		<title>Path to Eagle</title>
	</head>
	<body>
		<?php if (isset($message)) : ?>
		<div>
			<span><?= $message ?></span>
		</div>
		<?php endif; ?>
		<form method="POST">
			<select name="targetRank">
				<option value="">Select Rank</option>
				<option>Scout</option>
				<option>Tenderfoot</option>
				<option>Second Class</option>
				<option>First Class</option>
			</select>
			
			<input type="submit">
		</form>
	</body>
</html>