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
	
	function __construct($slug, $name, $progressOrder) {
		$this->slug = $slug;
		$this->name = $name;
		$this->progressOrder = $progressOrder;
	}
	
	public static function getRanks() {
		$ranks = array();
		
		$result = db::execute_reader("SELECT * FROM Ranks Order By ProgressionOrder ASC");
		if (FALSE === $result->error) {
			foreach ($result->rows as $row) {
				$ranks[] = new rank($row['Name'], $row['Name'], $row['ProgressionOrder']);
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
