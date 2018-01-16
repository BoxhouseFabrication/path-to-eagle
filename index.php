<html>
	<?php
		include 'utilities.php';
		include 'merit_badge.php';
		include 'rank.php';

		$allMBs = merit_badge::getMeritBadges();
		$eagleRequired = merit_badge::getEagleMeritBadges();
		$allRanks = rank::getRanks();
		$palmLimit = floor((count($allMBs)-count($eagleRequired))/5);
		
		$errorMsg = null;
		/*
		//IMPORT CURRENT ADVANCEMENT

		//SET TARGET ADVANCEMENT / TARGET DATE

		//SELECT MERIT BADGES

		//SUBMIT

		//RECEIVE PLAN PROJECTION
		1. List steps to be completed
		2. List date for last possible completion for each step
		3. List average days per requirement to be completed in order to match

		----------------------
		BACK END
		1. Determine starting rank.
		2. Determine completion rank.
		3. Gather list of all required steps between start and completion rank.
		4. Check off all requirements which have been completed.
		5. Determine all concurrent progression steps
		6. Divide remaining requirements amongst total time
		 */
		 
		if ("POST" === $_SERVER['REQUEST_METHOD']) {
			try {
				if (!isset($_POST['targetRank']) || empty($_POST['targetRank'])) {
					throw new Exception('Target Rank not specified');
				}
				
				$startingRank = 0;
				$targetRank = $_POST['targetRank'];
				
				$daysBetweenRanks = rank::getRankDays($startingRank, $targetRank);
				
				define('SHOW_RESULTS', true);//this should be the last thing that happens
			} catch (Exception $e) {
				define('SHOW_RESULTS', false);
				$errorMsg = $e->getMessage();
			}
		}
	?>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type='text/javascript'>
			function setTargetControls() {
				if ($("#advType_rank").is(":checked")) {
					$("#rankSelectors").show();
					$("#mbSelectors").hide();
					if ("Eagle Palms" === $("#targetRank").val()) {
						$("#targetPalmCount").show();
					} else {
						$("#targetPalmCount").hide();
					}
				} else {
					$("#rankSelectors").hide();
					$("#mbSelectors").show();
				}
			}
			
			function setTargetControlEventHandlers() {
				$("#advType_rank, #advType_merit_badge, #targetMB, #targetRank, #targetPalmCount").click(setTargetControls);
			}
			
			$(document).ready(function() {
				setTargetControls();
				setTargetControlEventHandlers();
			
			});
		</script>
	</head>
	<body>
		<form method='post'>
			<?php if ($errorMsg) : ?>
			<div class="error_message_container">
				<span class="error_message"><?= $errorMsg ?></span>
			</div>
			<?php endif; ?>
			<div>
				<label for='start_date'>Start Date: </label>
				<input type='date' name='start_date' min='<?= date('Y-m-d') ?>' value='<?= date('Y-m-d') ?>' />
			</div>

			<div>
				<label for='start_date'>Target Date: </label>
				<input type='date' name='target_date' />
			</div>

			<div>
				<span>Target Advancement Type:</span><br />
				<input type='radio' name='advType' id='advType_rank' checked='true'>
				<label for='advType_rank'>Rank</label>
				<input type='radio' name='advType' id='advType_merit_badge'>
				<label for='advType_merit_badge'>Merit Badge</label>
			</div>

			<div>
				<div id="mbSelectors" style="display:none">
					<select name='targetMB' id='targetMB'>
						<option value=''>Select Target Merit Badge</option>
						<?php foreach ($allMBs as $mb) : ?>
						<option value='<?= $mb['Name'] ?>'><?= $mb['Name'] ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div id="rankSelectors">
					<select name='targetRank' id='targetRank'>
						<option value=''>Select Target Rank</option>
						<?php foreach ($allRanks as $rank) : ?>
						<option value='<?= $rank->progressOrder ?>'><?= $rank->name ?></option>
						<?php endforeach; ?>
					</select>
					<select name='targetPalmCount' id='targetPalmCount' style="display:none;">
						<option value=''>Select Palm Count</option>
						<?php for ($p=0; $p<$palmLimit; $p++) : ?>
						<option><?= $p ?></option>
						<?php endfor; ?>
					</select>
				</div>
			</div>

			<div>
				<input type='submit'>
			</div>
		</form>
	</body>
</html>