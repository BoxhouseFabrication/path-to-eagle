<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of rank
 *
 * @author GregKrabach
 */
class rank {
    //put your code here
	public $slug;
	public $name;
	public $progressOrder;
        public $requiredDays;
        public $targetDate;
        public $electiveMbCnt;
        public $eagleMbCnt;
	
	function __construct($slug, $name = null, $progressOrder = 100, $requiredDays = 0, $electiveMbCnt = 0, $eagleMbCnt = 0) {
            $this->slug = utilities::sanitizeString($slug);
            $this->name = is_null($name) ? $slug : $name;
            $this->progressOrder = $progressOrder;
            $this->requiredDays = $requiredDays;
            $this->electiveMbCnt = $electiveMbCnt;
            $this->eagleMbCnt = $eagleMbCnt;
	}
	
	public static function getRanks() {
		$ranks = array();
		
		$result = db::execute_reader("SELECT * FROM Ranks Order By ProgressionOrder ASC");
		if (FALSE === $result->error) {
			foreach ($result->rows as $row) {
				$ranks[] = new rank($row['Name'], $row['Name'], $row['ProgressionOrder'], $row['RequiredDays']);
			}
		}

		return $ranks;
	}
        
        public static function getRanksToBeCompleted($startingRank, $targetRank) {
            $ranks = array();
		
            $result = db::execute_reader("SELECT * FROM Ranks WHERE ProgressionOrder > {$startingRank} AND
		ProgressionOrder <= {$targetRank} ORDER BY ProgressionOrder ASC");
            if (FALSE === $result->error) {
                    foreach ($result->rows as $row) {
                            $ranks[] = new rank($row['Name'], $row['Name'], $row['ProgressionOrder'], $row['RequiredDays'], $row['RequiredElectiveMeritBadges'], $row['RequiredEagleMeritBadges']);
                    }
            }

            return $ranks;
        }
	
	public static function getRankDays($startingRank, $targetRank) {
		$query = 
		"SELECT SUM(RequiredDays) as TotalRequiredDays
		FROM Ranks 
		WHERE ProgressionOrder > {$startingRank} AND
		ProgressionOrder <= {$targetRank}";
		
		$result = db::execute_reader($query);
		if (FALSE === $result->error) {
			return $result->rows[0]['TotalRequiredDays'];
		} else {
			return false;
		}
	}
}
