<?php

function gm_pixel_get_page_data($array, $index, $key) {
	if( !is_array($array) ) {
		return '';
	}

	if( !empty($array[$index][$key]) ) {
		return $array[$index][$key];
	}

	return '';
}