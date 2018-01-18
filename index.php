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
        global $form_values; 
        $form_values = array();
        
        $show_results = false;
        if ("POST" === $_SERVER['REQUEST_METHOD']) {
            try {
                if ($_POST['advType'] === 'rank' && !isset($_POST['targetRank']) || empty($_POST['targetRank'])) {
                    throw new Exception('Target Rank not specified');
                }
                if (!isset($_POST['target_date']) || empty($_POST['target_date'])) {
                    throw new Exception('Target Date not specified');//get the b-day and go to the 18th b-day as default?
                }

                $startingRank = 0;
                $targetRank = $_POST['targetRank'];

                $ranksToBeCompleted = rank::getRanksToBeCompleted($startingRank, $targetRank);

                $show_results = true;//this should be the last thing that happens
            } catch (Exception $e) {
                $errorMsg = $e->getMessage();
            }

            //restore submitted values
            $form_values = $_POST;
        } else {
            //setup non-blank defaults
            fv_set('start_date', date('Y-m-d'));
            fv_set('advType', 'rank');
        }
    ?>
    <head>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script type='text/javascript'>
                    function setTargetControls() {
                            if ($("#advType_rank").is(":checked")) {
                                    $("#rankSelectors").show();
                                    $("#mbSelectors").hide();
                                    if ("8" === $("#targetRank").val()) {
                                            $("#targetPalmCount").show();
                                    } else {
                                            $("#targetPalmCount").hide();
                                    }
                            } else {
                                    $("#rankSelectors").hide();
                                    $("#mbSelectors").show();
                                    $("#targetPalmCount").hide();
                            }
                    }
                    
                    function validatePlannedMbs() {
                        var selectedMbs = [];
                        $(".planned_mb").each(function(index, element) {
                           if ("" != $(this).val()) {
                                if (selectedMbs.indexOf($(this).val())== -1) {
                                    $(element).removeClass("mbSelectionConflict");
                                } else {
                                    $(element).addClass("mbSelectionConflict");
                                }
                                selectedMbs.push($(element).val());
                            }
                        });
                    }
                    
                    function plannedMBChanged(e) {
                        
                        if ("undefined" != typeof($("option:selected", this).data("days"))) {
                            $(this).siblings(".mbTime").html($("option:selected", this).data("days") + " days");
                        }
                        validatePlannedMbs();
                    }

                    function setEventHandlers() {
                        $("#advType_rank, #advType_merit_badge, #targetMB, #targetRank").click(setTargetControls);
                        $(".planned_mb").change(plannedMBChanged);
                    }

                    $(document).ready(function() {
                        setTargetControls();
                        setEventHandlers();
                        $(".planned_mb").each(plannedMBChanged);
                    });
            </script>
            <style>
                .path_rank {margin: 10px;}
                .mbSelectionConflict {background-color: red;}
            </style>
                
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
                <input type='date' name='start_date' min='<?= date('Y-m-d') ?>' value='<?= fv('start_date') ?>' />
            </div>

            <div>
                <label for='start_date'>Target Date: </label>
                <input type='date' name='target_date' value='<?= fv('target_date') ?>'/>
            </div>

            <div>
                <span>Target Advancement Type:</span><br />
                <input type='radio' name='advType' id='advType_rank' value='rank' <?php if (fv('advType') === 'rank') : ?>checked='true'<?php endif; ?>>
                <label for='advType_rank'>Rank</label>
                <input type='radio' name='advType' id='advType_merit_badge' value='merit_badge' <?php if (fv('advType') === 'merit_badge') : ?>checked='true'<?php endif; ?>>
                <label for='advType_merit_badge'>Merit Badge</label>
            </div>

            <div>
                <div id="mbSelectors" style="display:none">
                    <select name='targetMB' id='targetMB'>
                        <option value=''>Select Target Merit Badge</option>
                        <?php foreach ($allMBs as $mb) : ?>
                        <option value='<?= $mb['Name'] ?>' <?php if (fv('targetMB') === $mb['Name']):?>selected='true'<?php endif; ?>><?= $mb['Name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="rankSelectors">
                    <select name='targetRank' id='targetRank'>
                        <option value=''>Select Target Rank</option>
                        <?php foreach ($allRanks as $rank) : ?>
                        <option value='<?= $rank->progressOrder ?>' <?php if (fv('targetRank') === $rank->progressOrder):?>selected='true'<?php endif; ?>><?= $rank->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name='targetPalmCount' id='targetPalmCount' style="display:none;">
                        <option value=''>Select Palm Count</option>
                        <?php for ($p=0; $p<$palmLimit; $p++) : ?>
                        <option <?php if (fv('targetPalmCount') == $p):?>selected='true'<?php endif; ?>><?= $p ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div>
                    <input type='submit'>
            </div>

        <?php if ($show_results): ?>
            <?php 
                //define('PLANNING_METHOD', 'pessimistic');
                //define('PLANNING_METHOD', 'optimistic');
                define('PLANNING_METHOD', 'weighted');
                
                if("pessimistic" === PLANNING_METHOD) {
                    //figure out the last day that a rank can be completed...in reverse order.
                    //flip the rank array
                    //foreach rank subtract # of days from the target date/last rank date
                    $ranksToBeCompleted = array_reverse($ranksToBeCompleted);
                    $targetDate = fv('target_date');

                    foreach ($ranksToBeCompleted as $rank) {
                        $rank->targetDate = $targetDate;
                        $targetDate = date('Y-m-d', strtotime("-{$rank->requiredDays} day", strtotime($targetDate)));
                    }

                    $ranksToBeCompleted = array_reverse($ranksToBeCompleted);
                } else if("optimistic" === PLANNING_METHOD) {
                    $startDate = fv('start_date');
                    foreach ($ranksToBeCompleted as $rank) {
                        $startDate = date('Y-m-d', strtotime("+{$rank->requiredDays} day", strtotime($startDate)));
                        $rank->targetDate = $startDate;
                    }
                } else if ("weighted" === PLANNING_METHOD) {
                    $totalDaysRequired = 0;
                    foreach ($ranksToBeCompleted as $rank) {
                        $totalDaysRequired += $rank->requiredDays;
                    }
                    
                    $dateRangeStart= new DateTime(fv('start_date'));
                    $dateRangeEnd = new DateTime(fv('target_date'));
                    $dateDiff = $dateRangeEnd->diff($dateRangeStart);
                    $totalDaysAvailable = $dateDiff->days;
                    
                    $startDate = fv('start_date');
                    foreach ($ranksToBeCompleted as $rank) {
                        $startDate = date('Y-m-d', strtotime("+{$rank->requiredDays} day", strtotime($startDate)));
                        $rank->targetDate = $startDate;
                    }
                }
            ?>
        <div id="plannedPath">
            <?php foreach ($ranksToBeCompleted as $rank) : ?>
            <div class="path_rank">
                <span class="rank"><?= $rank->name ?></span> - <span class="targetDate">Complete By: <?= date('m/d/Y', strtotime($rank->targetDate)) ?><br>
                <?php /* $rank->requiredDays ?> / <?= $totalDaysRequired ?> = <?= $rank->requiredDays/$totalDaysRequired */?>
                <?php if (isset($totalDaysAvailable)) : ?>
                    <?php $weightedDays = $totalDaysAvailable * ($rank->requiredDays/$totalDaysRequired); ?>
                    <?= utilities::daysToDuration($weightedDays) ?>
                <?php endif; ?>
                <div class="merit_badges">
                    <?php if (0 < $rank->electiveMbCnt) : ?>
                    <span class="elective_merit_badges">Elective Merit Badges</span><br>
                    <?php for($m=0; $m<$rank->electiveMbCnt; $m++) : ?>
                    <div class="mbSelection">
                        <select name='<?= $rank->slug ?>_electiveMb_<?= $m ?>' id='<?= $rank->slug ?>_electiveMb_<?= $m ?>' class="planned_mb">
                            <option value=''>Select Merit Badge</option>
                            <?php 
                                foreach ($allMBs as $mb) : 
                                    if (!$mb['EagleRequired']) :
                            ?>
                            <option value='<?= $mb['Name'] ?>' <?php if (fv($rank->slug."_electiveMb_".$m) === $mb['Name']):?>selected='true'<?php endif; ?> data-days="<?= $mb['RequiredDays'] ?>"><?= $mb['Name'] ?></option>
                            <?php 
                                    endif; 
                                endforeach; 
                            ?>
                        </select><br>
                        <span class="mbTime"></span>
                    </div>
                    <?php endfor; ?><br />
                    <?php endif; ?>
                    <?php if (0 < $rank->eagleMbCnt) : ?>
                    <span class="eagle_merit_badges">Eagle Merit Badges</span><br>
                    <?php for($m=0; $m<$rank->eagleMbCnt; $m++) : ?>
                    <div class="mbSelection">
                        <select name='<?= $rank->slug ?>_eagleMb_<?= $m ?>' id='<?= $rank->slug ?>_eagleMb_<?= $m ?>' class="planned_mb">
                            <option value=''>Select Merit Badge</option>
                            <?php 
                                foreach ($allMBs as $mb) :
                                    if ($mb['EagleRequired']) :
                            ?>
                            <option value='<?= $mb['Name'] ?>' <?php if (fv($rank->slug."_eagleMb_".$m) === $mb['Name']):?>selected='true'<?php endif; ?> data-days="<?= $mb['RequiredDays'] ?>"><?= $mb['Name'] ?></option>
                            <?php 
                                    endif;
                                endforeach; 
                            ?>
                        </select><br />
                        <span class="mbTime"></span>
                    </div>
                    <?php endfor; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <input type="submit" value="Update" />
        </div>
            <?php utilities::dump($form_values); ?>
        <?php endif; ?>
        </form>
    </body>
</html>
<?php
    function fv_set($index, $value) {
        global $form_values;
        $form_values[$index] = $value;
    }
    
    function fv($index) {
        global $form_values;
        
        if (isset($form_values[$index])) {
            return $form_values[$index];
        } else {
            return '';
        }
    }
?>