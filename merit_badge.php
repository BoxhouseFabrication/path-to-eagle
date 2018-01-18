<?php
include 'db.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of merit_badge
 *
 * @author GregKrabach
 */

class merit_badge {
	//put your code here
	
	public $name;
	public $difficultyRating; // http://usscouts.org/advance/docs/Mr_DsReview.pdf (terminated: http://bsaprepared.com/BSA_PREPARED/MB_Difficulty.html)
	public $eagleRequired; //this is required for Eagle badge
	public $requiredDays; // https://meritbadge.org/
	public $requiredRank; 
	public $requiredAge;
	public $requiredBadge;
	
	function __construct( $name, $dr, $dayReq, $eagleReq, $rankReq, $ageReq, $badgeReq){
		$this->name = $name;
		$this->difficultyRating = $dr;
		$this->requiredDays = $dayReq;
		$this->eagleRequired = $eagleReq;
		$this->requiredRank = $rankReq;
		$this->requiredAge = $ageReq;
		$this->requiredBadge = $badgeReq;
	}
	
	public static function getMeritBadges() {
		$MBs = array();
		
		$result = db::execute_reader("SELECT * FROM Merit_Badge ORDER BY Name");
		if (FALSE === $result->error) {
			foreach ($result->rows as $row) {
				$MBs[] = $row;
			}
		}

		return $MBs;
	}
	
	public static function getEagleMeritBadges() {
		$MBs = array();
		
		$result = db::execute_reader("SELECT * FROM Merit_Badge WHERE EagleRequired = '1' ORDER BY Name");
		if (FALSE === $result->error) {
			foreach ($result->rows as $row) {
				$MBs[] = $row;
			}
		}

		return $MBs;
	}
}