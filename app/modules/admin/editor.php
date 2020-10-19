<?php
/**
 * @appointment Editor
 * @author      Alexandr Drozd
 * @copyright   Copyright Your Company 2020
 * @link        http://www.yoursite.com/
 * This code is copyrighted
 */
 
defined('ENGINE') or ('hacking attempt!');
 
 switch($route[1]){
	 
	/*
	*  Reparser
	*/
	 case 'parse':
		echo '<pre>';
		print_r(get_meta_tags($_SERVER['REQUEST_SCHEME'].'://yoursite.com'));
		die;
		$dir = ROOT_DIR.'/uploads/images/editor/thumb_';
		$images = [];
		foreach(glob($dir."*") as $image){
			$or_img = str_replace('thumb_', '', $image);
			$new_img = str_replace('thumb_', 'preview_', $image);
			$img = new Imagick($or_img);
			$img->cropThumbnailImage(200, 200);
			$img->stripImage();
			$img->writeImage($new_img);
			$img->destroy();
			echo '<p><img src="'.str_replace(ROOT_DIR, '', $new_img).'"></p>';
		}
		die;
	 break;
	 
	/*
	*  Delete image
	*/
	 case 'del_image':
		if(preg_match("/\/uploads\/images\/editor\/thumb_([A-Za.0-9]+).(jpeg|jpg|png|gif)/i", $_POST['image'], $m)){
			unlink(ROOT_DIR.'/uploads/images/editor/thumb_'.$m[1].'.'.$m[2]);
		}
	 break;
	 
	/*
	*  List image
	*/
	 case 'list':
		$dir = ROOT_DIR.'/uploads/images/editor/thumb_';
		$images = [];
		foreach(glob($dir."*") as $image){
			$images[] = str_replace(ROOT_DIR, '', $image);
		}
		echo json_encode($images);
	 break;
	 
	/*
	*  Upload image
	*/
	 default:
		// path
		$dir = ROOT_DIR.'/uploads/images/editor/';

		// temp file
		$tmp = $_FILES['image']['tmp_name'];

		$type = mb_strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

		// Check
		if(!preg_match("/image\/(jpeg|jpg|png|gif)/i", getimagesize($tmp)['mime']) OR !in_array($type, ['jpeg', 'jpg', 'png', 'gif'])){
			die(json_encode([
				'error' => 'err_image_type'
			]));
		}

		if($_FILES['image']['size'] >= 1024*20*1024){
			die(json_encode([
				'error' => 'err_file_size'
			]));
		}

		// New name
		$rename = uniqid('', true).'.jpg';

		// Upload image
		if(move_uploaded_file($tmp, $dir.$rename)){
			
			$img = new Imagick($dir.$rename);
			
			if($img->getImageWidth() > 1920){
				$img->resizeImage(1920, 0, imagick::FILTER_LANCZOS, 0.9);
			}
			
			auto_rotate_image($img);
			$img->stripImage();
			$img->setCompressionQuality(70);
			$img->setFormat("jpg");
			$img->writeImage($dir.$rename);
			
			$img->cropThumbnailImage(200, 200);
			$img->writeImage($dir.'preview_'.$rename);
			
			$img->cropThumbnailImage(94, 94);
			$img->writeImage($dir.'thumb_'.$rename);
			$img->destroy();
			
			echo json_encode([
				'error' => 0,
				'image' => '/uploads/images/editor/'.$rename
			]);
		}
 }
 die;
 ?>