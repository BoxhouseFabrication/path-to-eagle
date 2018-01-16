<?php

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
class db_results {
	public $error;
	public $rowCnt;
	public $rows = array();
}

class db {
	
	public static function execute_reader($query) {
		$mysqli = new mysqli("localhost", "wordpress", "wordpress", "bsa_eagle_path");


		/* check connection */
		if ($mysqli->connect_errno) {
			printf("Connect failed: %s\n", $mysqli->connect_error);
			exit();
		}

		/* If we have to retrieve large amount of data we use MYSQLI_USE_RESULT */
		$result = $mysqli->query($query, MYSQLI_USE_RESULT);
		
		$db = new db_results();
		if ($result) {
			$db->error = false;
			while ($row = $result->fetch_array()) {
				$db->rows[] = $row;
				$db->rowCnt++;
			}
		} else {
			$db->error = true;
		}
		
		$mysqli->close();
	
		return $db;
	}
}