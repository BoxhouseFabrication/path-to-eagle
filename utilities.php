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

class utilities {
	
	public static function dump($obj) {
		echo "<pre>";
		var_dump($obj);
		echo "</pre>";
	}
        
        public static function daysToDuration($dayCnt) {
            $duration['year'] = floor($dayCnt/365);
            $dayCnt = $dayCnt-($duration['year']*365);
            
            $duration['month'] = floor($dayCnt/30);
            $dayCnt = $dayCnt-($duration['month']*30);
            
            $duration['week'] = floor($dayCnt/7);
            $dayCnt = $dayCnt-($duration['week']*7);
            
            $duration['day'] = ceil($dayCnt);
            $dString = "";
            foreach ($duration as $key=>$value) {
                if (0 < $value) {
                    if (!empty($dString)) {
                        $dString .= ", ";
                    }
                    $dString .= "{$value} {$key}";
                    if (1 < $value) {
                        $dString .= "s";
                    }
                }
            }
            return $dString;
        }
        
        public static function sanitizeString($str) {
            $str = str_replace(' ', '_', $str);
            return $str;
        }
}