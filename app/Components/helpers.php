<?php


function debug($data,$die=0){
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		if($die==1){
			die;
		}
	}


?>