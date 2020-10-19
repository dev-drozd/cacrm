<?php
/**
 * @appointment Imbox admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2019
 * @link        http://yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

switch($route[1]){
	default:
		tpl_set('imbox/main', [
			'id' => 0,
		], [
		], 'content');
}

