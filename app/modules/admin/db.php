<?php
/**
 * @appointment Forms admin panel
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');

 switch($route[1]) {
     case 'dub':
        $meta['title'] = 'Dublicates';
        tpl_set('db/main', [
        ], [], 'content');
     break;
 }
?>